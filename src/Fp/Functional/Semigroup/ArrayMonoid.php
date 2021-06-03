<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TK of array-key
 * @template TV
 *
 * @implements Monoid<array<TK, TV>>
 * @psalm-immutable
 */
class ArrayMonoid implements Monoid
{
    /**
     * @psalm-param array<TK, TV> $value
     */
    public function __construct(private array $value)
    {
    }

    public function empty(): array
    {
        return [];
    }

    /**
     * @param array<TK, TV> $rhs
     * @return array<TK, TV>
     */
    public function combineOne(mixed $rhs): array
    {
        return array_merge($this->value, $rhs);
    }

    /**
     * @template TKK of array-key
     * @template TVV
     *
     * @param array<TKK, TVV> $lhs
     * @param array<TKK, TVV> $rhs
     * @return array<TKK, TVV>
     */
    public static function combine(mixed $lhs, mixed $rhs): array
    {
        $that = new self($lhs);

        return $that->combineOne($rhs);
    }

    /**
     * @return array<TK, TV>
     */
    public function extract(): array
    {
        return $this->value;
    }
}
