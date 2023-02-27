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
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Generator;
use Psalm\Type;

use function Fp\Evidence\of;
use function Fp\Evidence\classStringOf;
use function Fp\Collection\first;
use function Fp\Collection\second;

final class GetCollectionTypeParams
{
    /**
     * @return Option<CollectionTypeParams>
     */
    public static function keyValue(Type\Union $union): Option
    {
        return GetCollectionTypeParams::value($union)
            ->map(fn($val_type) => new CollectionTypeParams(
                key_type: GetCollectionTypeParams::key($union)->getOrCall(fn() => Type::getArrayKey()),
                val_type: $val_type
            ));
    }

    /**
     * @return Option<Type\Union>
     */
    public static function key(Type\Union $union): Option
    {
        return PsalmApi::$types
            ->asSingleAtomic($union)
            ->flatMap(fn($atomic) => Option::firstT(
                fn() => self::keyFromIterable($atomic),
                fn() => self::keyFromGenerator($atomic),
                fn() => self::keyFromArray($atomic),
                fn() => self::keyFromGenericObject($atomic),
                fn() => self::keyFromKeyedArray($atomic),
            ));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function keyFromIterable(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TIterable::class))
            ->flatMap(fn(Type\Atomic\TIterable $a) => first($a->type_params));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function keyFromGenerator(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TGenericObject::class))
            ->filter(fn(Type\Atomic\TGenericObject $generic) => $generic->value === Generator::class)
            ->flatMap(fn(Type\Atomic\TGenericObject $generic) => first($generic->type_params));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function keyFromArray(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TArray::class))
            ->flatMap(fn(Type\Atomic\TArray $a) => first($a->type_params));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function keyFromKeyedArray(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TKeyedArray::class))
            ->map(fn(Type\Atomic\TKeyedArray $a) => $a->getGenericKeyType());
    }

    /**
     * @return Option<Type\Union>
     */
    private static function keyFromGenericObject(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TGenericObject::class))
            ->flatMap(
                fn(Type\Atomic\TGenericObject $a) => Option::some($a->value)
                    ->flatMap(classStringOf([Map::class, NonEmptyMap::class]))
                    ->flatMap(fn() => second($a->type_params)),
            );
    }

    /**
     * @return Option<Type\Union>
     */
    public static function value(Type\Union $union): Option
    {
        return PsalmApi::$types
            ->asSingleAtomic($union)
            ->flatMap(fn($atomic) => Option::firstT(
                fn() => self::valueFromIterable($atomic),
                fn() => self::valueFromGenerator($atomic),
                fn() => self::valueFromArray($atomic),
                fn() => self::valueFromList($atomic),
                fn() => self::valueFromGenericObject($atomic),
                fn() => self::valueFromKeyedArray($atomic),
            ));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function valueFromIterable(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TIterable::class))
            ->flatMap(fn(Type\Atomic\TIterable $a) => second($a->type_params));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function valueFromGenerator(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TGenericObject::class))
            ->filter(fn(Type\Atomic\TGenericObject $generic) => $generic->value === Generator::class)
            ->flatMap(fn(Type\Atomic\TGenericObject $generic) => second($generic->type_params));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function valueFromArray(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TArray::class))
            ->flatMap(fn(Type\Atomic\TArray $a) => second($a->type_params));
    }

    /**
     * @return Option<Type\Union>
     */
    private static function valueFromList(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TList::class))
            ->map(fn(Type\Atomic\TList $a) => $a->type_param);
    }

    /**
     * @return Option<Type\Union>
     */
    private static function valueFromKeyedArray(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TKeyedArray::class))
            ->map(fn(Type\Atomic\TKeyedArray $a) => $a->getGenericValueType());
    }

    /**
     * @return Option<Type\Union>
     */
    private static function valueFromGenericObject(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->flatMap(of(Type\Atomic\TGenericObject::class))
            ->flatMap(fn(Type\Atomic\TGenericObject $a) => match (true) {
                is_subclass_of($a->value, Seq::class),
                is_subclass_of($a->value, Set::class),
                is_subclass_of($a->value, NonEmptySet::class),
                is_subclass_of($a->value, NonEmptySeq::class),
                is_subclass_of($a->value, Option::class) => first($a->type_params),
                is_subclass_of($a->value, Map::class),
                is_subclass_of($a->value, NonEmptyMap::class),
                is_subclass_of($a->value, Either::class) => second($a->type_params),
                default => Option::none(),
            });
    }
}
