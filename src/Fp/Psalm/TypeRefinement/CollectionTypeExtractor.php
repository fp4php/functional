<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use Fp\Collections\Map;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Option\Option;
use Fp\Psalm\Psalm;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

use function Fp\classOf;

/**
 * @psalm-type CollectionTypeParameters = array{Union, Union}
 */
final class CollectionTypeExtractor
{
    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    public static function extract(Union $union): Option
    {
        return Psalm::getUnionSingeAtomic($union)
            ->flatMap(fn($a) => self::fromList($a)
                ->orElse(fn() => self::fromArrayOrIterable($a))
                ->orElse(fn() => self::fromOption($a))
                ->orElse(fn() => self::fromSeq($a))
                ->orElse(fn() => self::fromSet($a))
                ->orElse(fn() => self::fromMap($a))
            );
    }

    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    private static function fromList(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof TList)
            ->map(fn($a) => new CollectionTypeParams(Type::getInt(), $a->type_param));
    }

    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    private static function fromArrayOrIterable(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof TArray || $a instanceof TIterable)
            ->map(fn($a) => new CollectionTypeParams($a->type_params[0], $a->type_params[1]));
    }

    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    private static function fromOption(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof TGenericObject)
            ->filter(fn($a) => $a->value === Option::class)
            ->filter(fn($a) => 1 === count($a->type_params))
            ->map(fn($a) => new CollectionTypeParams(Type::getArrayKey(), $a->type_params[0]));
    }

    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    private static function fromSeq(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof TGenericObject)
            ->filter(fn($a) => classOf($a->value, Seq::class) || classOf($a->value, NonEmptySeq::class))
            ->filter(fn($a) => 1 === count($a->type_params))
            ->map(fn($a) => new CollectionTypeParams(Type::getArrayKey(), $a->type_params[1]));
    }

    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    private static function fromSet(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof TGenericObject)
            ->filter(fn($a) => classOf($a->value, Set::class) || classOf($a->value, NonEmptySet::class))
            ->filter(fn($a) => 1 === count($a->type_params))
            ->map(fn($a) => new CollectionTypeParams(Type::getArrayKey(), $a->type_params[1]));
    }

    /**
     * @psalm-return Option<CollectionTypeParams>
     */
    private static function fromMap(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof TGenericObject)
            ->filter(fn($a) => classOf($a->value, Map::class) || classOf($a->value, NonEmptyMap::class))
            ->filter(fn($a) => 2 === count($a->type_params))
            ->map(fn($a) => new CollectionTypeParams($a->type_params[0], $a->type_params[1]));
    }
}
