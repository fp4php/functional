<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Collections\Map;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Option\Option;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

/**
 * Psalm helper methods
 *
 * @internal
 */
class PsalmTypeParam
{
    /**
     * @psalm-return Option<array{Union, Union}>
     */
    public static function getUnionKeyValueTypeParams(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $key = yield self::getUnionKeyTypeParam($union);
            $value = yield self::getUnionValueTypeParam($union);
            return [$key, $value];
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getUnionKeyTypeParam(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $atomic = yield Psalm::getUnionSingeAtomic($union);

            return yield self::filterTIterableKeyTypeParam($atomic)
                ->orElse(fn() => self::filterTArrayKeyTypeParam($atomic))
                ->orElse(fn() => self::filterTGenericObjectKeyTypeParam($atomic))
                ->orElse(fn() => self::filterTKeyedArrayKeyTypeParam($atomic));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getUnionValueTypeParam(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $atomic = yield Psalm::getUnionSingeAtomic($union);

            return yield self::filterTIterableValueTypeParam($atomic)
                ->orElse(fn() => self::filterTArrayValueTypeParam($atomic))
                ->orElse(fn() => self::filterTListValueTypeParam($atomic))
                ->orElse(fn() => self::filterTGenericObjectValueTypeParam($atomic))
                ->orElse(fn() => self::filterTKeyedArrayValueTypeParam($atomic));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTIterableKeyTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TIterable)
            ->map(fn(TIterable $a) => $a->type_params[0]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTArrayKeyTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TArray)
            ->map(fn(TArray $a) => $a->type_params[0]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTKeyedArrayKeyTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TKeyedArray)
            ->map(fn(TKeyedArray $a) => $a->getGenericKeyType());
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTGenericObjectKeyTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TGenericObject)
            ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                is_a($a->value, Map::class, true) => $a->type_params[1],
                default => null
            }));
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTIterableValueTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TIterable)
            ->map(fn(TIterable $a) => $a->type_params[1]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTArrayValueTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TArray)
            ->map(fn(TArray $a) => $a->type_params[1]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTListValueTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TList)
            ->map(fn(TList $a) => $a->type_param);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTKeyedArrayValueTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TKeyedArray)
            ->map(fn(TKeyedArray $a) => $a->getGenericValueType());
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function filterTGenericObjectValueTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TGenericObject)
            ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                is_a($a->value, Seq::class, true) => $a->type_params[0],
                is_a($a->value, Set::class, true) => $a->type_params[0],
                is_a($a->value, Map::class, true) => $a->type_params[1],
                is_a($a->value, NonEmptySeq::class, true) => $a->type_params[0],
                is_a($a->value, NonEmptySet::class, true) => $a->type_params[0],
                is_a($a->value, Option::class, true) => $a->type_params[0],
                default => null
            }));
    }
}
