<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TK of array-key
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends Semigroup<non-empty-array<TK, TV>>
 */
class NonEmptyArraySemigroup extends Semigroup
{
    /**
     * @psalm-param non-empty-array<TK, TV> $lhs
     * @psalm-param non-empty-array<TK, TV> $rhs
     * @psalm-return non-empty-array<TK, TV>
     */
    public function combine(mixed $lhs, mixed $rhs): array
    {
        return array_merge($lhs, $rhs);
    }
}
