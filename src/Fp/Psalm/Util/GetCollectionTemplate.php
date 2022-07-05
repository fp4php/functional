<?php

declare(strict_types=1);

namespace Fp\Psalm\Util;

use Fp\Collections\Map;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;
use function Fp\classOf;

final class GetCollectionTemplate
{
    /**
     * @psalm-return Option<Union>
     */
    public static function key(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $atomic = yield PsalmApi::$types->asSingleAtomic($union);

            return yield self::keyFromIterable($atomic)
                ->orElse(fn() => self::keyFromArray($atomic))
                ->orElse(fn() => self::keyFromGenericObject($atomic))
                ->orElse(fn() => self::keyFromKeyedArray($atomic));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function keyFromIterable(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TIterable::class)
            ->map(fn(TIterable $a) => $a->type_params[0]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function keyFromArray(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TArray::class)
            ->map(fn(TArray $a) => $a->type_params[0]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function keyFromKeyedArray(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TKeyedArray::class)
            ->map(fn(TKeyedArray $a) => $a->getGenericKeyType());
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function keyFromGenericObject(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TGenericObject::class)
            ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                classOf($a->value, Map::class) => $a->type_params[1],
                classOf($a->value, NonEmptyMap::class) => $a->type_params[1],
                default => null
            }));
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function value(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $atomic = yield PsalmApi::$types->asSingleAtomic($union);

            return yield self::valueFromIterable($atomic)
                ->orElse(fn() => self::valueFromArray($atomic))
                ->orElse(fn() => self::valueFromList($atomic))
                ->orElse(fn() => self::valueFromGenericObject($atomic))
                ->orElse(fn() => self::valueFromKeyedArray($atomic));
        });
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function valueFromIterable(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TIterable::class)
            ->map(fn(TIterable $a) => $a->type_params[1]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function valueFromArray(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TArray::class)
            ->map(fn(TArray $a) => $a->type_params[1]);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function valueFromList(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TList::class)
            ->map(fn(TList $a) => $a->type_param);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function valueFromKeyedArray(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TKeyedArray::class)
            ->map(fn(TKeyedArray $a) => $a->getGenericValueType());
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function valueFromGenericObject(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filterOf(TGenericObject::class)
            ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                classOf($a->value, Seq::class) => $a->type_params[0],
                classOf($a->value, Set::class) => $a->type_params[0],
                classOf($a->value, Map::class) => $a->type_params[1],
                classOf($a->value, NonEmptyMap::class) => $a->type_params[1],
                classOf($a->value, NonEmptySeq::class) => $a->type_params[0],
                classOf($a->value, NonEmptySet::class) => $a->type_params[0],
                classOf($a->value, Option::class) => $a->type_params[0],
                classOf($a->value, Either::class) => $a->type_params[1],
                default => null
            }));
    }
}
