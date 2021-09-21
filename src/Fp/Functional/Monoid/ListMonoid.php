<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

/**
 * @template TV
 * @extends Monoid<list<TV>>
 * @psalm-immutable
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
     * @psalm-pure
     * @psalm-param list<TV> $lhs
     * @psalm-param list<TV> $rhs
     * @psalm-return list<TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return [...$lhs, ...$rhs];
    }
}

