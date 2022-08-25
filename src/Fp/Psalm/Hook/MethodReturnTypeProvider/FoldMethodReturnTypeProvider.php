<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Option\Option;
use Fp\Operations\FoldOperation;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Fp\Streams\Stream;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Psalm\Issue\InvalidReturnStatement;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TEmpty;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TMixed;
use Psalm\Type\Atomic\TNever;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Cast\asList;
use function Fp\Collection\first;
use function Fp\Collection\last;
use function Fp\Collection\map;
use function Fp\Collection\second;
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class FoldMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [
            Seq::class,
            LinkedList::class,
            ArrayList::class,
            NonEmptySeq::class,
            NonEmptyLinkedList::class,
            NonEmptyArrayList::class,
            FoldOperation::class,
            Set::class,
            HashSet::class,
            NonEmptySet::class,
            NonEmptyHashSet::class,
            Map::class,
            HashMap::class,
            NonEmptyMap::class,
            NonEmptyHashMap::class,
            Stream::class,
        ];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        return self::removeLiteralsFromTInit($event)
            ->orElse(fn() => self::foldReturnType($event))
            ->get();
    }

    /**
     * @return Option<Union>
     */
    private static function foldReturnType(MethodReturnTypeProviderEvent $event): Option
    {
        // $list->fold(0)(fn($acc, $cur) => $acc + $cur)
        //             ^                         ^
        //             |                         |
        //             |                         |
        //            TInit                    TFold (return type of function)
        return proveTrue(FoldOperation::class === $event->getFqClasslikeName())

            // Get TInit and TFold
            ->flatMap(
                fn() => sequenceOption([
                    fn() => second($event->getTemplateTypeParameters() ?? []),
                    fn() => proveOf($event->getStmt(), MethodCall::class)
                        ->map(fn(MethodCall $call) => $call->getArgs())
                        ->flatMap(fn(array $args) => first($args))
                        ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
                        ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomicOf(TClosure::class, $type))
                        ->flatMap(fn(TClosure $closure) => Option::fromNullable($closure->return_type))
                        ->map(fn(Union $type) => PsalmApi::$types->asNonLiteralType($type))
                        ->flatMap(fn(Union $type) => PsalmApi::$types->asSingleAtomic($type))
                        ->map(fn(Atomic $atomic) => new Union([$atomic]))
                ])
            )

            // TFold must be assignable to TInit
            ->tapN(function(Union $TInit, Union $TFold) use ($event) {
                // $fold = $integers->fold(ArrayList::empty()) === FoldingOperation<int, ArrayList<never>> (second param is IInit)
                // $fold(fn($list, $num) => $list->appended($num + 1));
                //                                  ^
                //                                  |
                //                                  |
                //                           Type will be ArrayList<int> (It is TFold)
                //
                // We must check that the TFold is assignable to the TInit.
                // But ArrayList<int> cannot be assigned to ArrayList<never> because the never is supertype of the int.
                // The neverToMixed swaps never type to mixed (Hope this won't be a problem)
                if (PsalmApi::$types->isTypeContainedByType($TFold, self::neverToMixed($TInit))) {
                    return;
                }

                $source = $event->getSource();

                IssueBuffer::accepts(
                    e: new InvalidReturnStatement(
                        message: "The inferred type '{$TFold->getId()}' does not match the declared return type '{$TInit->getId()}'",
                        code_location: $event->getCodeLocation(),
                    ),
                    suppressed_issues: $source->getSuppressedIssues(),
                );
            })

            // Fold return type will be subsume of the TFold and the TInit
            // when TInit = ArrayList<never>
            //  and TFold = ArrayList<int>
            // then ArrayList<never> | ArrayList<int> = ArrayList<int>
            ->map(fn($types) => Type::combineUnionTypeArray($types, PsalmApi::$codebase));
    }

    /**
     * @return Option<Union>
     */
    private static function removeLiteralsFromTInit(MethodReturnTypeProviderEvent $event): Option
    {
        // $fold = $integers->fold(0) === FoldingOperation<int, 0>
        // $fold(fn($sum, $num) => $sum + $num);
        //                              ^
        //                              |
        //                              |
        //                       Type will be the int
        //
        // In the example above TInit is the int type, but TInit is the 0 (literal type)
        // The int is not assignable to 0.
        // It also extends to other literal types.
        // Next code maps any literal types to non-literal analog.
        // Then $integers->fold(0) will be FoldingOperation<int, int>
        return proveTrue('fold' === $event->getMethodNameLowercase())
            ->flatMap(fn() => sequenceOption([
                fn() => last($event->getTemplateTypeParameters() ?? []),
                fn() => proveOf($event->getStmt(), MethodCall::class)
                    ->flatMap(fn(MethodCall $call) => first($call->getArgs()))
                    ->flatMap(fn(Arg $arg) => PsalmApi::$args->getArgType($event, $arg))
                    ->map(fn(Union $type) => PsalmApi::$types->asNonLiteralType($type))
            ]))
            ->mapN(fn(Union $A, Union $TInit) => [
                new TGenericObject(FoldOperation::class, [$A, $TInit]),
            ])
            ->map(ctor(Union::class));
    }

    private static function neverToMixed(Union $type): Union
    {
        return new Union(
            map(asList($type->getAtomicTypes()), fn(Atomic $a) => match (true) {
                $a instanceof TKeyedArray => $a->is_list
                    ? new TNonEmptyList(
                        self::neverToMixed($a->getGenericValueType()),
                    )
                    : new TNonEmptyArray([
                        self::neverToMixed($a->getGenericKeyType()),
                        self::neverToMixed($a->getGenericValueType()),
                    ]),
                $a instanceof TNonEmptyList => new TNonEmptyList(
                    self::neverToMixed($a->type_param),
                ),
                $a instanceof TList => new TList(
                    self::neverToMixed($a->type_param),
                ),
                $a instanceof TNonEmptyArray => new TNonEmptyArray([
                    self::neverToMixed($a->type_params[0]),
                    self::neverToMixed($a->type_params[1]),
                ]),
                $a instanceof TArray => new TArray([
                    self::neverToMixed($a->type_params[0]),
                    self::neverToMixed($a->type_params[1]),
                ]),
                $a instanceof TGenericObject => new TGenericObject(
                    $a->value,
                    map($a->type_params, fn(Union $t) => self::neverToMixed($t)),
                ),
                default => $a instanceof TNever || $a instanceof TEmpty ? new TMixed() : $a,
            }),
        );
    }
}
