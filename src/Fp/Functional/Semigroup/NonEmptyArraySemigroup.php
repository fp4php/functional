<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TK of array-key
 * @template TV
 * @extends Semigroup<non-empty-array<TK, TV>>
 */
class NonEmptyArraySemigroup extends Semigroup
{
    /**
     * @param non-empty-array<TK, TV> $lhs
     * @param non-empty-array<TK, TV> $rhs
     * @return non-empty-array<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return array_merge($lhs, $rhs);
    }
}
