<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template T
 * @extends Semigroup<T>
 */
class RhsSemigroup extends Semigroup
{
    /**
     * @param T $lhs
     * @param T $rhs
     * @return T
     */
    public function combine(mixed $lhs, mixed $rhs): mixed
    {
        return $rhs;
    }
}
