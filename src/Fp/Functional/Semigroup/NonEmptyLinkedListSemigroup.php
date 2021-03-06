<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

use Fp\Collections\NonEmptyLinkedList;

/**
 * @template TV
 * @psalm-immutable
 * @extends Semigroup<NonEmptyLinkedList<TV>>
 */
class NonEmptyLinkedListSemigroup extends Semigroup
{
    /**
     * @psalm-pure
     * @psalm-param NonEmptyLinkedList<TV> $lhs
     * @psalm-param NonEmptyLinkedList<TV> $rhs
     * @psalm-return NonEmptyLinkedList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): NonEmptyLinkedList
    {
        return $rhs->prependedAll($lhs);
    }
}
