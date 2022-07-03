<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyArrayList;

/**
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends Semigroup<NonEmptyArrayList<TV>>
 */
class NonEmptyArrayListSemigroup extends Semigroup
{
    /**
     * @param NonEmptyArrayList<TV> $lhs
     * @param NonEmptyArrayList<TV> $rhs
     * @return NonEmptyArrayList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyArrayList
    {
        return $lhs->appendedAll($rhs);
    }
}
