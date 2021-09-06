<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\PureIterable;
use Generator;

/**
 * @template TV
 *
 * @extends Semigroup<NonEmptyHashSet<TV>>
 * @psalm-immutable
 */
class NonEmptyHashSetSemigroup extends Semigroup
{
    /**
     * @psalm-pure
     *
     * @psalm-param NonEmptyHashSet<TV> $lhs
     * @psalm-param NonEmptyHashSet<TV> $rhs
     *
     * @psalm-return NonEmptyHashSet<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe(PureIterable::of(function () use ($rhs, $lhs): Generator {
            foreach ($lhs as $elem) {
                yield $elem;
            }
            foreach ($rhs as $elem) {
                yield $elem;
            }
        }));
    }
}
