<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

/**
 * @template TK of array-key
 * @template TV
 * @extends Monoid<array<TK, TV>>
 */
class ArrayMonoid extends Monoid
{
    public function empty(): array
    {
        return [];
    }

    /**
     * @param array<TK, TV> $lhs
     * @param array<TK, TV> $rhs
     * @return array<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return array_merge($lhs, $rhs);
    }
}
