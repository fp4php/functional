<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\LinkedList;
use Fp\Collections\Nil;

/**
 * @template TV
 * @extends Monoid<LinkedList<TV>>
 * @psalm-immutable
 */
class LinkedListMonoid extends Monoid
{
    /**
     * @psalm-return LinkedList<TV>
     */
    public function empty(): LinkedList
    {
        return Nil::getInstance();
    }

    /**
     * @psalm-pure
     * @psalm-param LinkedList<TV> $lhs
     * @psalm-param LinkedList<TV> $rhs
     * @psalm-return LinkedList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): LinkedList
    {
        return $rhs->prependedAll($lhs);
    }
}

