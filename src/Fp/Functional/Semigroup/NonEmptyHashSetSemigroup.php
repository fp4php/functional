<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyHashSet;
use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TV
 * @extends Semigroup<NonEmptyHashSet<TV>>
 */
class NonEmptyHashSetSemigroup extends Semigroup
{
    /**
     * @param NonEmptyHashSet<TV> $lhs
     * @param NonEmptyHashSet<TV> $rhs
     * @return NonEmptyHashSet<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyHashSet
    {
        return NonEmptyHashSet::collectUnsafe(asGenerator(function () use ($rhs, $lhs): Generator {
            foreach ($lhs as $elem) {
                yield $elem;
            }
            foreach ($rhs as $elem) {
                yield $elem;
            }
        }));
    }
}
