<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyLinkedList;

/**
 * @template TV
 * @extends Semigroup<NonEmptyLinkedList<TV>>
 */
class NonEmptyLinkedListSemigroup extends Semigroup
{
    /**
     * @param NonEmptyLinkedList<TV> $lhs
     * @param NonEmptyLinkedList<TV> $rhs
     * @return NonEmptyLinkedList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyLinkedList
    {
        return $rhs->prependedAll($lhs);
    }
}
