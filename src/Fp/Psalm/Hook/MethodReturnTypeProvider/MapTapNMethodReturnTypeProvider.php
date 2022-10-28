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
use Fp\Psalm\Util\MapTapNContextEnum;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Issue\IfThisIsMismatch;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Storage\FunctionLikeParameter;
use Psalm\Type;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\every;
use function Fp\Collection\exists;
use function Fp\Collection\keys;
use function Fp\Collection\last;
use function Fp\Evidence\proveFalse;
use function Fp\Evidence\proveNonEmptyList;
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
        Option::do(function() use ($event) {
            $templates = yield proveTrue(self::isSupportedMethod($event))
                ->flatMap(fn() => proveNonEmptyList($event->getTemplateTypeParameters() ?? []));

            // Take the most right template:
            //    Option<A>    -> A
            //    Either<E, A> -> A
            //    Map<K, A>    -> A
            $current_args = yield last($templates)
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->filterOf(TKeyedArray::class)
                ->filter(fn(TKeyedArray $keyed) => self::isTuple($keyed) || self::isAssoc($keyed))
                ->orElse(fn() => self::valueTypeIsNotValidKeyedArrayIssue($event));

            $current_args_kind = self::isTuple($current_args)
                ? MapTapNContextEnum::Tuple
                : MapTapNContextEnum::Shape;

            // $callback mapN/tapN argument
            $map_callback = yield PsalmApi::$args->getCallArgs($event)
                ->flatMap(fn(ArrayList $args) => $args->firstElement())
                ->pluck('type')
                ->flatMap(PsalmApi::$types->asSingleAtomic(...))
                ->filterOf([TCallable::class, TClosure::class]);

            // Input tuple type inferred by $callback argument
            $func_args = yield Option::some($map_callback)
                ->flatMap(fn(TCallable|TClosure $func) => ArrayList::collect($func->params ?? [])
                    ->zipWithKeys()
                    ->reindexN(fn(int $idx, FunctionLikeParameter $param) => $current_args_kind === MapTapNContextEnum::Shape ? $param->name : $idx)
                    ->mapN(function(int $_, FunctionLikeParameter $param) use ($current_args_kind) {
                        $param_type = $param->type ?? Type::getMixed();

                        return $current_args_kind === MapTapNContextEnum::Shape && $param->is_optional
                            ? PsalmApi::$types->asPossiblyUndefined($param_type)
                            : $param_type;
                    })
                    ->toNonEmptyArray())
                ->map(ctor(TKeyedArray::class));

            $ctx = new MapTapNContext(
                event: $event,
                func_args: $func_args,
                current_args: $current_args,
                kind: $current_args_kind,
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

            proveFalse($ctx->is_variadic && self::isAssoc($current_args))
                ->orElse(fn() => self::cannotSafelyCallShapeWithVariadicArg($ctx));

            // Assert that $func_args is assignable to $current_args
            proveTrue(self::isTypeContainedByType($ctx))
                ->orElse(fn() => self::typesAreNotCompatibleIssue($ctx));
        });

        return null;
    }

    private static function isSupportedMethod(MethodReturnTypeProviderEvent $event): bool
    {
        $methods = [
            'mapN',
            'tapN',
            'flatMapN',
            'flatTapN',
            'reindexN',
            'filterN',
            'filterMapN',
            'everyN',
            'existsN',
            'traverseOptionN',
            'traverseEitherN',
            'partitionN',
            'partitionMapN',
            'firstN',
            'lastN',
        ];

        return exists($methods, fn($method) => $event->getMethodNameLowercase() === strtolower($method));
    }

    private static function isTuple(TKeyedArray $keyed): bool
    {
        return array_is_list($keyed->properties) && $keyed->is_list && $keyed->sealed;
    }

    private static function isAssoc(TKeyedArray $keyed): bool
    {
        return every(keys($keyed->properties), is_string(...));
    }

    /**
     * @return Option<never>
     */
    private static function valueTypeIsNotValidKeyedArrayIssue(MethodReturnTypeProviderEvent $event): Option
    {
        $mappable_class = $event->getFqClasslikeName();
        $source = $event->getSource();

        $issue = new IfThisIsMismatch(
            message: "Value template of class {$mappable_class} must be tuple (all keys int from 0 to N) or shape (all keys is string)",
            code_location: $event->getCodeLocation(),
        );

        IssueBuffer::accepts($issue, $source->getSuppressedIssues());
        return Option::none();
    }

    /**
     * @return Option<never>
     */
    private static function cannotSafelyCallShapeWithVariadicArg(MapTapNContext $ctx): Option
    {
        $source = $ctx->event->getSource();

        $issue = new IfThisIsMismatch(
            message: 'Shape cannot safely passed to function with variadic parameter.',
            code_location: $ctx->event->getCodeLocation(),
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

        $tuned_func_args = $ctx->kind->tuneForOptionalAndVariadicParams($ctx);

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
                $context->kind->tuneForOptionalAndVariadicParams($context),
            ]),
        );
    }
}
