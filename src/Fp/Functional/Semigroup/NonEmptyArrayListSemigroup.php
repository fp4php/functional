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
     * @psalm-param NonEmptyArrayList<TV> $lhs
     * @psalm-param NonEmptyArrayList<TV> $rhs
     * @psalm-return NonEmptyArrayList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyArrayList
    {
        return $lhs->appendedAll($rhs);
    }
}
