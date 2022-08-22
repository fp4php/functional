<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\LinkedList;
use Fp\Collections\Set;
use Fp\Collections\ArrayList;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\MapTapNContext;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Issue\IfThisIsMismatch;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Storage\FunctionLikeParameter;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Collection\dropRight;
use function Fp\Collection\init;
use function Fp\Collection\last;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyList;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class MapTapNMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [
            Seq::class,
            ArrayList::class,
            LinkedList::class,
            NonEmptySeq::class,
            NonEmptyArrayList::class,
            NonEmptyLinkedList::class,
            Set::class,
            HashSet::class,
            NonEmptySet::class,
            NonEmptyHashSet::class,
            Map::class,
            HashMap::class,
            NonEmptyMap::class,
            NonEmptyHashMap::class,
            Option::class,
            Either::class,
        ];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        $return_type = Option::do(function() use ($event) {
            $templates = yield proveTrue(self::isSupportedMethod($event))
                ->flatMap(fn() => proveNonEmptyList($event->getTemplateTypeParameters() ?? []));

            // Take the most right template:
            //    Option<A>    -> A
            //    Either<E, A> -> A
            //    Map<K, A>    -> A
            $current_args = yield last($templates)
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->filterOf(TKeyedArray::class)
                ->filter(self::isTuple(...))
                ->orElse(fn() => self::valueTypeIsNotTupleIssue($event));

            // $callback mapN/tapN argument
            $map_callback = yield PsalmApi::$args->getCallArgs($event)
                ->flatMap(fn(ArrayList $args) => $args->firstElement())
                ->pluck('type')
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->flatMap(fn(Atomic $atomic) => proveOf($atomic, [TCallable::class, TClosure::class]));

            // Input tuple type inferred by $callback argument
            $func_args = yield Option::some($map_callback)
                ->flatMap(fn(TCallable|TClosure $func) => ArrayList::collect($func->params ?? [])
                    ->zipWithKeys()
                    ->reindex(fn(array $tuple) => $current_args->is_list ? $tuple[0] : $tuple[1]->name)
                    ->map(fn(array $tuple) => $tuple[1]->type ?? Type::getMixed())
                    ->toNonEmptyArray())
                ->map(fn(array $types) => new TKeyedArray($types));

            $ctx = new MapTapNContext(
                event: $event,
                func_args: $func_args,
                current_args: $current_args,
                is_variadic: last($map_callback->params ?? [])
                    ->pluck('is_variadic')
                    ->getOrElse(false),
                optional_count: ArrayList::collect($map_callback->params ?? [])
                    ->filter(fn(FunctionLikeParameter $p) => $p->is_optional)
                    ->count(),
                required_count: ArrayList::collect($map_callback->params ?? [])
                    ->filter(fn(FunctionLikeParameter $p) => !$p->is_optional)
                    ->count(),
            );

            // Assert that $func_args is assignable to $current_args
            proveTrue(self::isTypeContainedByType($ctx))
                ->orElse(fn() => self::typesAreNotCompatibleIssue($ctx));

            // Change most right template if the mapN/flatMapN was call:
            //    Option<A>    -> Option<B>
            //    Either<E, A> -> Either<E, B>
            //    Map<K, A>    -> Map<K, B>
            return new Union([
                new TGenericObject($event->getFqClasslikeName(), self::isMapN($event) || self::isFlatMapN($event)
                    ? [
                        ...init($templates),
                        Option::fromNullable($map_callback->return_type)
                            ->filter(fn() => self::isFlatMapN($event))
                            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                            ->filterOf(TGenericObject::class)
                            ->flatMap(fn(TGenericObject $generic) => last($generic->type_params))
                            ->orElse(fn() => Option::fromNullable($map_callback->return_type))
                            ->getOrCall(fn() => Type::getMixed()),
                    ]
                    : $templates,
                ),
            ]);
        });

        return $return_type->get();
    }

    private static function isSupportedMethod(MethodReturnTypeProviderEvent $event): bool
    {
        return self::isMapN($event)
            || self::isTapN($event)
            || self::isFlatMapN($event)
            || self::isFlatTapN($event);
    }

    private static function isTuple(TKeyedArray $keyed): bool
    {
        return array_is_list($keyed->properties) && $keyed->is_list && $keyed->sealed;
    }

    private static function isMapN(MethodReturnTypeProviderEvent $event): bool
    {
        return $event->getMethodNameLowercase() === strtolower('mapN');
    }

    private static function isFlatMapN(MethodReturnTypeProviderEvent $event): bool
    {
        return $event->getMethodNameLowercase() === strtolower('flatMapN');
    }

    private static function isTapN(MethodReturnTypeProviderEvent $event): bool
    {
        return $event->getMethodNameLowercase() === strtolower('tapN');
    }

    private static function isFlatTapN(MethodReturnTypeProviderEvent $event): bool
    {
        return $event->getMethodNameLowercase() === strtolower('flatTapN');
    }

    /**
     * @return Option<never>
     */
    private static function valueTypeIsNotTupleIssue(MethodReturnTypeProviderEvent $event): Option
    {
        $mappable_class = $event->getFqClasslikeName();
        $source = $event->getSource();

        $issue = new IfThisIsMismatch(
            message: "Value template of class {$mappable_class} must be tuple",
            code_location: $event->getCodeLocation(),
        );

        IssueBuffer::accepts($issue, $source->getSuppressedIssues());
        return Option::none();
    }

    /**
     * @return Option<never>
     */
    private static function typesAreNotCompatibleIssue(MapTapNContext $ctx): Option
    {
        $mappable_class = $ctx->event->getFqClasslikeName();
        $source = $ctx->event->getSource();

        $tuned_func_args = self::tuneForOptionalAndVariadicParams($ctx);

        $issue = new IfThisIsMismatch(
            message: implode(', ', [
                "Object must be type of {$mappable_class}<{$tuned_func_args->getId()}>",
                "actual type {$mappable_class}<{$ctx->current_args->getId()}>",
            ]),
            code_location: $ctx->event->getCodeLocation(),
        );

        IssueBuffer::accepts($issue, $source->getSuppressedIssues());
        return Option::none();
    }

    private static function isTypeContainedByType(MapTapNContext $context): bool
    {
        return PsalmApi::$types->isTypeContainedByType(
            new Union([$context->current_args]),
            new Union([
                self::tuneForOptionalAndVariadicParams($context),
            ]),
        );
    }

    private static function tuneForOptionalAndVariadicParams(MapTapNContext $ctx): TKeyedArray
    {
        return self::tuneForOptional(
            func_args: $ctx->is_variadic
                ? self::tuneForVariadic($ctx->func_args, $ctx->current_args)
                : $ctx->func_args,
            drop_length: $ctx->optional_count > 0
                ? $ctx->required_count + $ctx->optional_count - count($ctx->current_args->properties)
                : 0,
        );
    }

    private static function tuneForOptional(TKeyedArray $func_args, int $drop_length): TKeyedArray
    {
        $propsOption = proveNonEmptyList(dropRight($func_args->properties, $drop_length));

        $cloned = clone $func_args;
        $cloned->properties = $propsOption->getOrElse($cloned->properties);

        return $cloned;
    }

    private static function tuneForVariadic(TKeyedArray $func_args, TKeyedArray $current_args): TKeyedArray
    {
        $func_args_count = count($func_args->properties);
        $current_args_count = count($current_args->properties);

        $propsOption = match (true) {
            // There are variadic args: extend type with variadic param type
            $current_args_count > $func_args_count => last($func_args->properties)
                ->map(fn($last) => [
                    ...$func_args->properties,
                    ...array_fill(0, $current_args_count - $func_args_count, $last),
                ]),

            // No variadic args: remove variadic param from type
            $current_args_count < $func_args_count => proveNonEmptyArray(init($func_args->properties)),

            // Exactly one variadic arg: leave as is
            default => Option::none(),
        };

        $cloned = clone $func_args;
        $cloned->properties = $propsOption->getOrElse($cloned->properties);

        return $cloned;
    }
}
