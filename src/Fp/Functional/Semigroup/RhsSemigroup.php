<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template T
 * @psalm-immutable
 * @extends Semigroup<T>
 */
class RhsSemigroup extends Semigroup
{
    /**
     * @psalm-pure
     * @psalm-param T $lhs
     * @psalm-param T $rhs
     * @psalm-return T
     */
    public function combine(mixed $lhs, mixed $rhs): mixed
    {
        return $rhs;
    }
}
