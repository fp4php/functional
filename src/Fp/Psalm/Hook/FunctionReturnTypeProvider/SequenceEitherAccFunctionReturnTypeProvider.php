<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Sequence\GetEitherTypeParam;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\sequenceOption;

final class SequenceEitherAccFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\sequenceEitherAcc'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getFirstCallArgType($event)
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->filterOf(TKeyedArray::class)
            ->flatMap(fn(TKeyedArray $either_shape) => sequenceOption([
                self::mapEither($either_shape, GetEitherTypeParam::GET_LEFT),
                self::mapEither($either_shape, GetEitherTypeParam::GET_RIGHT),
            ]))
            ->map(fn(array $type_params) => [
                new TGenericObject(Either::class, $type_params),
            ])
            ->map(ctor(Union::class))
            ->get();
    }

    /**
     * @param GetEitherTypeParam::GET_* $idx
     * @return Option<Union>
     */
    private static function mapEither(TKeyedArray $either_shape, int $idx): Option
    {
        return NonEmptyHashMap::collectNonEmpty($either_shape->properties)
            ->traverseOption(fn($property_type) => GetEitherTypeParam::from($property_type, $idx))
            ->map(fn(NonEmptyHashMap $properties) => $properties->map(
                fn(Union $property_type) => $idx === GetEitherTypeParam::GET_LEFT
                    ? PsalmApi::$types->asPossiblyUndefined($property_type)
                    : $property_type
            ))
            ->map(function(NonEmptyHashMap $properties) use ($idx) {
                $is_list = $properties->keys()->every(is_int(...));

                $keyed = new TKeyedArray($properties->toNonEmptyArray());
                $keyed->is_list = $is_list;
                $keyed->sealed = $is_list;

                return new Union([$keyed]);
            });
    }
}
