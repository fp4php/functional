<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\NonEmptyHashMap;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

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
            ->filterOf(TKeyedArray::class)
            ->filter(fn(TKeyedArray $keyed) => !$keyed->is_list)
            ->map(fn(TKeyedArray $keyed) => NonEmptyHashMap::collectNonEmpty($keyed->properties)
                ->map(function(Union $property) {
                    if (!$property->isNullable()) {
                        return $property;
                    }

                    $possibly_undefined = clone $property;
                    $possibly_undefined->removeType('null');
                    $possibly_undefined->possibly_undefined = true;

                    return $possibly_undefined;
                })
                ->toNonEmptyArray())
            ->map(ctor(TKeyedArray::class))
            ->map(fn($keyed) => new Union([$keyed]))
            ->get();
    }
}
