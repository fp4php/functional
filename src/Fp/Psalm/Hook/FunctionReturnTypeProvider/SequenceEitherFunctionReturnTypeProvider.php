<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Sequence\GetEitherTypeParam;
use Fp\PsalmToolkit\CallArg;
use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\contains;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\of;
use function Fp\Evidence\proveTrue;

final class SequenceEitherFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\sequenceEither'),
            strtolower('Fp\Collection\sequenceEitherT'),
            strtolower('Fp\Collection\sequenceEitherMerged'),
            strtolower('Fp\Collection\sequenceEitherMergedT'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return self::getInputTypeFromSequenceEither($event)
            ->orElse(fn() => self::getInputTypeFromSequenceEitherT($event))
            ->flatMap(fn(TKeyedArray $types) => sequenceOptionT(
                fn() => NonEmptyHashMap::collectNonEmpty($types->properties)
                    ->traverseOption(GetEitherTypeParam::left(...))
                    ->map(fn(NonEmptyHashMap $props) => $props->values()->toNonEmptyList())
                    ->map(fn(array $left_cases) => Type::combineUnionTypeArray($left_cases, PsalmApi::$codebase)),
                fn() => NonEmptyHashMap::collectNonEmpty($types->properties)
                    ->traverseOption(GetEitherTypeParam::right(...))
                    ->map(function(NonEmptyHashMap $props) {
                        return new Union([
                            new TKeyedArray(
                                properties: $props->toNonEmptyArray(),
                                is_list: $props->keys()->every(is_int(...)),
                            ),
                        ]);
                    }),
            ))
            ->map(fn(array $type_params) => [
                new TGenericObject(Either::class, $type_params),
            ])
            ->map(ctor(Union::class))
            ->get();
    }

    /**
     * @return Option<TKeyedArray>
     */
    private static function getInputTypeFromSequenceEither(FunctionReturnTypeProviderEvent $event): Option
    {
        $isSequenceEither = contains($event->getFunctionId(), [
            strtolower('Fp\Collection\sequenceEither'),
            strtolower('Fp\Collection\sequenceEitherMerged'),
        ]);

        return proveTrue($isSequenceEither)
            ->map(fn() => PsalmApi::$args->getCallArgs($event))
            ->flatMap(fn($args) => $args->head())
            ->flatMap(fn(CallArg $arg) => PsalmApi::$types->asSingleAtomic($arg->type))
            ->flatMap(of(TKeyedArray::class));
    }

    /**
     * @return Option<TKeyedArray>
     */
    private static function getInputTypeFromSequenceEitherT(FunctionReturnTypeProviderEvent $event): Option
    {
        $isSequenceEitherT = contains($event->getFunctionId(), [
            strtolower('Fp\Collection\sequenceEitherT'),
            strtolower('Fp\Collection\sequenceEitherMergedT'),
        ]);

        return proveTrue($isSequenceEitherT)
            ->map(fn() => PsalmApi::$args->getCallArgs($event))
            ->flatMap(fn(ArrayList $args) => $args->toNonEmptyArrayList())
            ->map(fn(NonEmptyArrayList $args) => new TKeyedArray(
                $args->map(fn(CallArg $arg) => $arg->type)->toNonEmptyList(),
            ));
    }
}
