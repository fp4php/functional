<?php

declare(strict_types=1);

namespace Fp\Psalm\TypeRefinement;

use Fp\Functional\Option\Option;
use Fp\Psalm\Psalm;
use Psalm\Type;

/**
 * @psalm-type CollectionTypeParameters = array{Type\Union, Type\Union}
 */
final class CollectionTypeExtractor
{
    /**
     * @return Option<CollectionTypeParams>
     */
    public static function extract(Type\Union $union): Option
    {
        return Psalm::getSingeAtomic($union)
            ->flatMap(fn($a) => self::fromList($a)
                ->orElse(fn() => self::fromArrayOrIterable($a))
                ->orElse(fn() => self::fromOption($a))
            );
    }

    /**
     * @return Option<CollectionTypeParams>
     */
    private static function fromList(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof Type\Atomic\TList)
            ->map(fn($a) => new CollectionTypeParams(Type::getInt(), $a->type_param));
    }

    /**
     * @return Option<CollectionTypeParams>
     */
    private static function fromArrayOrIterable(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof Type\Atomic\TArray || $a instanceof Type\Atomic\TIterable)
            ->map(fn($a) => new CollectionTypeParams($a->type_params[0], $a->type_params[1]));
    }

    /**
     * @return Option<CollectionTypeParams>
     */
    private static function fromOption(Type\Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn($a) => $a instanceof Type\Atomic\TGenericObject)
            ->filter(fn($a) => $a->value === Option::class)
            ->filter(fn($a) => 1 === count($a->type_params))
            ->map(fn($a) => new CollectionTypeParams(Type::getArrayKey(), $a->type_params[0]));
    }
}
