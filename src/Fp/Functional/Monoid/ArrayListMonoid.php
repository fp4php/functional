<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

use Fp\Collections\ArrayList;

/**
 * @template TV
 * @extends Monoid<ArrayList<TV>>
 * @psalm-suppress InvalidTemplateParam
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
     * @psalm-param ArrayList<TV> $lhs
     * @psalm-param ArrayList<TV> $rhs
     * @psalm-return ArrayList<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): ArrayList
    {
        return $lhs->appendedAll($rhs);
    }
}

