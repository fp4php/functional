<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Collections\Map;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Union;

use function Fp\classOf;

/**
 * @internal
 */
trait TypeParamValueExtractor
{
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
