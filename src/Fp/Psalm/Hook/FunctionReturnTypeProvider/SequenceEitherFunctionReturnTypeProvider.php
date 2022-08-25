<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\FunctionReturnTypeProvider;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\CallArg;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\FunctionReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;

use function Fp\Callable\ctor;
use function Fp\Collection\at;
use function Fp\Collection\sequenceOption;

final class SequenceEitherFunctionReturnTypeProvider implements FunctionReturnTypeProviderInterface
{
    private const PARAM_LEFT = 0;
    private const PARAM_RIGHT = 1;

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
                    ->traverseOption(fn(Union $type) => self::getEitherTypeParam($type, self::PARAM_LEFT))
                    ->map(fn(NonEmptyHashMap $props) => $props->values()->toNonEmptyList())
                    ->map(fn(array $left_cases) => Type::combineUnionTypeArray($left_cases, PsalmApi::$codebase)),
                fn() => NonEmptyHashMap::collectNonEmpty($types->properties)
                    ->traverseOption(fn(Union $type) => self::getEitherTypeParam($type, self::PARAM_RIGHT))
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

    /**
     * @param self::PARAM_* $idx
     * @return Option<Union>
     */
    private static function getEitherTypeParam(Union $type, int $idx): Option
    {
        return PsalmApi::$types->asSingleAtomic($type)
            ->flatMap(fn(Type\Atomic $atomic) => match (true) {
                $atomic instanceof TGenericObject => Option::some($atomic)
                    ->filter(fn(TGenericObject $generic) => $generic->value === Either::class)
                    ->flatMap(fn(TGenericObject $option) => at($option->type_params, $idx)),
                $atomic instanceof TClosure => Option::fromNullable($atomic->return_type)
                    ->flatMap(fn(Union $t) => self::getEitherTypeParam($t, $idx)),
                default => Option::none(),
            });
    }
}
