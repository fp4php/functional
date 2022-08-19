<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use function Fp\Collection\first;
use function Fp\Collection\traverseOption;

final class SequenceOptionFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\sequenceOption'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return PsalmApi::$args->getCallArgs($event)
            ->flatMap(fn($args) => $args->head())
            ->flatMap(fn(CallArg $arg) => PsalmApi::$types->asSingleAtomicOf(TKeyedArray::class, $arg->type))
            ->flatMap(fn(TKeyedArray $types) => traverseOption(
                $types->properties,
                fn(Union $type) => PsalmApi::$types
                    ->asSingleAtomicOf(TGenericObject::class, $type)
                    ->filter(fn(TGenericObject $generic) => $generic->value === Option::class)
                    ->flatMap(fn(TGenericObject $option) => first($option->type_params))
            ))
            ->map(function(array $mapped) {
                $is_list = array_is_list($mapped);

                $keyed = new TKeyedArray($mapped);
                $keyed->is_list = $is_list;
                $keyed->sealed = $is_list;

                return new Union([
                    new TGenericObject(Option::class, [
                        new Union([$keyed]),
                    ]),
                ]);
            })
            ->get();
    }
}
