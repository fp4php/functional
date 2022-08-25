<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use Psalm\Type\Atomic;

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
            ->pluck('type')
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->filterOf(TKeyedArray::class)
            ->flatMap(fn(TKeyedArray $types) => traverseOption($types->properties, self::getOptionTypeParam(...)))
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

    /**
     * @return Option<Union>
     */
    private static function getOptionTypeParam(Union $type): Option
    {
        return PsalmApi::$types->asSingleAtomic($type)
            ->flatMap(fn(Atomic $atomic) => match (true) {
                $atomic instanceof TGenericObject => Option::some($atomic)
                    ->filter(fn(TGenericObject $generic) => $generic->value === Option::class)
                    ->flatMap(fn(TGenericObject $option) => first($option->type_params)),
                $atomic instanceof TClosure => Option::fromNullable($atomic->return_type)
                    ->flatMap(self::getOptionTypeParam(...)),
                default => Option::none(),
            });
    }
}
