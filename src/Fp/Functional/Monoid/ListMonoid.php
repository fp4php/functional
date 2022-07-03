<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

/**
 * @template TV
 * @extends Monoid<list<TV>>
 */
class ListMonoid extends Monoid
{
    /**
     * @psalm-return list<TV>
     */
    public function empty(): array
    {
        return [];
    }

    /**
     * @param list<TV> $lhs
     * @param list<TV> $rhs
     * @return list<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return [...$lhs, ...$rhs];
    }
}

