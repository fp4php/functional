<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\PureIterable;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends Semigroup<NonEmptyHashMap<TK, TV>>
 * @psalm-immutable
 */
class NonEmptyHashMapSemigroup extends Semigroup
{
    /**
     * @psalm-pure
     *
     * @psalm-param NonEmptyHashMap<TK, TV> $lhs
     * @psalm-param NonEmptyHashMap<TK, TV> $rhs
     *
     * @psalm-return NonEmptyHashMap<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyHashMap
    {
        return NonEmptyHashMap::collectPairsUnsafe(PureIterable::of(function () use ($rhs, $lhs) {
            foreach ($lhs as $pair) {
                yield $pair;
            }
            foreach ($rhs as $pair) {
                yield $pair;
            }
        }));
    }
}
