<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TV
 * @extends Semigroup<non-empty-list<TV>>
 */
class NonEmptyListSemigroup extends Semigroup
{
    /**
     * @param non-empty-list<TV> $lhs
     * @param non-empty-list<TV> $rhs
     * @return non-empty-list<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return [...$lhs, ...$rhs];
    }
}
