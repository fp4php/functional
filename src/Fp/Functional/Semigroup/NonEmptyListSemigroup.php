<?php

declare(strict_types=1);

namespace Fp\Functional\Semigroup;

/**
 * @template TV
 *
 * @implements Semigroup<non-empty-list<TV>>
 * @psalm-immutable
 */
class NonEmptyListSemigroup implements Semigroup
{
    /**
     * @psalm-param non-empty-list<TV> $value
     */
    public function __construct(private array $value)
    {
    }

    /**
     * @param non-empty-list<TV> $rhs
     * @return non-empty-list<TV>
     */
    public function combineOne(mixed $rhs): array
    {
        return [...$this->value, ...$rhs];
    }

    /**
     * @psalm-pure
     * @template TVV
     *
     * @param non-empty-list<TVV> $lhs
     * @param non-empty-list<TVV> $rhs
     * @return non-empty-list<TVV>
     */
    public static function combine(mixed $lhs, mixed $rhs): array
    {
        $that = new self($lhs);

        return $that->combineOne($rhs);
    }

    /**
     * @return non-empty-list<TV>
     */
    public function extract(): array
    {
        return $this->value;
    }

    public function combineOneSemi(Semigroup $rhs): Semigroup
    {
        return new NonEmptyListSemigroup($this->combineOne($rhs->extract()));
    }
}
