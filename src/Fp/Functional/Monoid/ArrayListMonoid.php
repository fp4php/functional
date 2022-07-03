<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\ArrayList;

/**
 * @template TV
 * @extends Monoid<ArrayList<TV>>
 */
class ArrayListMonoid extends Monoid
{
    /**
     * @psalm-return ArrayList<TV>
     */
    public function empty(): ArrayList
    {
        return new ArrayList([]);
    }

    /**
     * @param ArrayList<TV> $lhs
     * @param ArrayList<TV> $rhs
     * @return ArrayList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): ArrayList
    {
        return $lhs->appendedAll($rhs);
    }
}

