<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Collections\Map;
use Fp\Collections\NonEmptyMap;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;

use function Fp\classOf;

/**
 * @internal
 */
trait TypeParamKeyExtractor
{
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
                classOf($a->value, Map::class) => $a->type_params[1],
                classOf($a->value, NonEmptyMap::class) => $a->type_params[1],
                default => null
            }));
    }
}
