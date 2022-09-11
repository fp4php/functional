<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Either\Either;
use Fp\Psalm\Util\Sequence\GetEitherTypeParam;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\sequenceOption;

final class SequenceEitherFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\sequenceEither'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn($args) => $args->head())
            ->flatMap(fn(CallArg $arg) => PsalmApi::$types->asSingleAtomicOf(TKeyedArray::class, $arg->type))
            ->flatMap(fn(TKeyedArray $types) => sequenceOption([
                fn() => NonEmptyHashMap::collectNonEmpty($types->properties)
                    ->traverseOption(GetEitherTypeParam::left(...))
                    ->map(fn(NonEmptyHashMap $props) => $props->values()->toNonEmptyList())
                    ->map(fn(array $left_cases) => Type::combineUnionTypeArray($left_cases, PsalmApi::$codebase)),
                fn() => NonEmptyHashMap::collectNonEmpty($types->properties)
                    ->traverseOption(GetEitherTypeParam::right(...))
                    ->map(function(NonEmptyHashMap $props) {
                        $is_list = $props->keys()->every(is_int(...));

                        $keyed = new TKeyedArray($props->toNonEmptyArray());
                        $keyed->is_list = $is_list;
                        $keyed->sealed = $is_list;

                        return new Union([$keyed]);
                    }),
            ]))
            ->map(fn(array $type_params) => [
                new TGenericObject(Either::class, $type_params),
            ])
            ->map(ctor(Union::class))
            ->get();
    }
}
