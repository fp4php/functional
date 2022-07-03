<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\LinkedList;
use Fp\Collections\Nil;

/**
 * @template TV
 * @extends Monoid<LinkedList<TV>>
 */
class LinkedListMonoid extends Monoid
{
    /**
     * @return LinkedList<TV>
     */
    public function empty(): LinkedList
    {
        return Nil::getInstance();
    }

    /**
     * @param LinkedList<TV> $lhs
     * @param LinkedList<TV> $rhs
     * @return LinkedList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): LinkedList
    {
        return $rhs->prependedAll($lhs);
    }
}

