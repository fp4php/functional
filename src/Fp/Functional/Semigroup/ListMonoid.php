<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TV
 *
 * @implements Monoid<list<TV>>
 * @psalm-immutable
 */
class ListMonoid implements Monoid
{
    /**
     * @psalm-param list<TV> $value
     */
    public function __construct(private array $value)
    {
    }

    public function empty(): array
    {
        return [];
    }

    /**
     * @param list<TV> $rhs
     * @return list<TV>
     */
    public function combineOne(mixed $rhs): array
    {
        return [...$this->value, ...$rhs];
    }

    /**
     * @template TVV
     *
     * @param list<TVV> $lhs
     * @param list<TVV> $rhs
     * @return list<TVV>
     */
    public static function combine(mixed $lhs, mixed $rhs): array
    {
        $that = new self($lhs);

        return $that->combineOne($rhs);
    }

    /**
     * @return list<TV>
     */
    public function extract(): array
    {
        return $this->value;
    }

    public function combineOneSemi(Semigroup $rhs): Semigroup
    {
        return new ListMonoid($this->combineOne($rhs->extract()));
    }
}
