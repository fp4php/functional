<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\NonEmptyHashMap;
use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\Evidence\of;
use function Fp\Callable\ctor;

final class FilterNotNullFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\filterNotNull'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getFirstCallArgType($event)
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->flatMap(of(TKeyedArray::class))
            ->filter(fn(TKeyedArray $keyed) => !$keyed->is_list)
            ->map(fn(TKeyedArray $keyed) => NonEmptyHashMap::collectNonEmpty($keyed->properties)
                ->map(function(Union $property) {
                    if (!$property->isNullable()) {
                        return $property;
                    }

                    return PsalmApi::$types->asNonNullable($property)->setPossiblyUndefined(true);
                })
                ->toNonEmptyArray())
            ->map(ctor(TKeyedArray::class))
            ->map(fn($keyed) => new Union([$keyed]))
            ->get();
    }
}
