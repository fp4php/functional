<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Collections\ArrayList;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptySet;
use Fp\Functional\Option\Option;
use Psalm\Type\Atomic\TLiteralFloat;
use Psalm\Type\Atomic\TLiteralInt;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Union;

/**
 * @internal
 */
trait LiteralExtractor
{
    /**
     * @psalm-return Option<NonEmptySet<int|float|string>>
     */
    public static function getUnionLiteralValues(Union $union): Option
    {
        $literalValues = ArrayList::collect($union->getLiteralStrings())
            ->appendedAll($union->getLiteralFloats())
            ->appendedAll($union->getLiteralInts())
            ->map(fn(TLiteralString|TLiteralFloat|TLiteralInt $literal) => $literal->value);

        return NonEmptyHashSet::collect($literalValues);
    }

    /**
     * @psalm-return Option<int|float|string>
     */
    public static function getUnionSingleLiteralValue(Union $union): Option
    {
        $someUnion = Option::some($union);

        return $someUnion
            ->filter(fn(Union $union) => $union->isSingleStringLiteral())
            ->orElse(function () use ($someUnion) {
                return $someUnion->filter(
                    fn(Union $union) => $union->isSingleFloatLiteral()
                );
            })
            ->orElse(function () use ($someUnion) {
                return $someUnion->filter(
                    fn(Union $union) => $union->isSingleIntLiteral()
                );
            })
            ->flatMap(fn(Union $type) => self::getUnionLiteralValues($type))
            ->map(fn(NonEmptySet $literals) => $literals->head());
    }
}
