<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\CallArg;
use Fp\PsalmToolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use Psalm\Type\Atomic;

use function Fp\Evidence\of;
use function Fp\Collection\first;
use function Fp\Collection\traverseOption;
use function Fp\Evidence\proveTrue;

final class SequenceOptionFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    public static function getFunctionIds(): array
    {
        return [
            strtolower('Fp\Collection\sequenceOption'),
            strtolower('Fp\Collection\sequenceOptionT'),
        ];
    }

    public static function getFunctionReturnType(FunctionReturnTypeProviderEvent $event): ?Union
    {
        return self::getInputTypeFromSequenceOption($event)
            ->orElse(fn() => self::getInputTypeFromSequenceOptionT($event))
            ->flatMap(fn(TKeyedArray $types) => traverseOption($types->properties, self::getOptionTypeParam(...)))
            ->map(function(array $mapped) {
                return new Union([
                    new TGenericObject(Option::class, [
                        new Union([
                            new TKeyedArray($mapped, is_list: array_is_list($mapped)),
                        ]),
                    ]),
                ]);
            })
            ->get();
    }

    /**
     * @return Option<TKeyedArray>
     */
    private static function getInputTypeFromSequenceOption(FunctionReturnTypeProviderEvent $event): Option
    {
        return proveTrue(strtolower('Fp\Collection\sequenceOption') === $event->getFunctionId())
            ->map(fn() => PsalmApi::$args->getCallArgs($event))
            ->flatMap(fn(ArrayList $args) => $args->head())
            ->map(fn(CallArg $arg) => $arg->type)
            ->flatMap(PsalmApi::$types->asSingleAtomic(...))
            ->flatMap(of(TKeyedArray::class));
    }

    /**
     * @return Option<TKeyedArray>
     */
    private static function getInputTypeFromSequenceOptionT(FunctionReturnTypeProviderEvent $event): Option
    {
        return proveTrue(strtolower('Fp\Collection\sequenceOptionT') === $event->getFunctionId())
            ->map(fn() => PsalmApi::$args->getCallArgs($event))
            ->flatMap(fn(ArrayList $args) => $args->toNonEmptyArrayList())
            ->map(fn(NonEmptyArrayList $args) => new TKeyedArray(
                $args->map(fn(CallArg $arg) => $arg->type)->toNonEmptyList(),
            ));
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
