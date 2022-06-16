<?php

declare(strict_types=1);

namespace Fp\Functional\Monoid;

/**
 * @template TK of array-key
 * @template TV
 * @extends Monoid<array<TK, TV>>
 * @psalm-suppress InvalidTemplateParam
 */
class ArrayMonoid extends Monoid
{
    public function empty(): array
    {
        return [];
    }

    /**
     * @psalm-param array<TK, TV> $lhs
     * @psalm-param array<TK, TV> $rhs
     * @psalm-return array<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return array_merge($lhs, $rhs);
    }
}
