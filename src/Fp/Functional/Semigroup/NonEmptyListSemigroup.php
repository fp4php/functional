<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TV
 *
 * @extends Semigroup<non-empty-list<TV>>
 * @psalm-immutable
 */
class NonEmptyListSemigroup extends Semigroup
{
    /**
     * @psalm-pure
     *
     * @psalm-param non-empty-list<TV> $lhs
     * @psalm-param non-empty-list<TV> $rhs
     *
     * @psalm-return non-empty-list<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return [...$lhs, ...$rhs];
    }
}
