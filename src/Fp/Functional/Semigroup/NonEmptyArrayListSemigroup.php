<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyArrayList;

/**
 * @template TV
 *
 * @extends Semigroup<NonEmptyArrayList<TV>>
 * @psalm-immutable
 */
class NonEmptyArrayListSemigroup extends Semigroup
{
    /**
     * @psalm-pure
     *
     * @psalm-param NonEmptyArrayList<TV> $lhs
     * @psalm-param NonEmptyArrayList<TV> $rhs
     *
     * @psalm-return NonEmptyArrayList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyArrayList
    {
        return $lhs->appendedAll($rhs);
    }
}
