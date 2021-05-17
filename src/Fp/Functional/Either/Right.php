<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant L
 * @template-covariant R
 * @psalm-immutable
 * @extends Either<L, R>
 */
final class Right extends Either
{
    /**
     * @psalm-param R $value
     */
    public function __construct(protected int|float|bool|string|object|array $value) {}

    /**
     * @template LI
     * @template RI
     * @psalm-param RI $value
     * @psalm-return self<LI, RI>
     */
    public static function of(int|float|bool|string|object|array $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-return R
     */
    public function get(): int|float|bool|string|object|array
    {
        return $this->value;
    }
}
