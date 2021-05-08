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
     * @var R
     */
    private int|float|bool|string|object $value;

    /**
     * @psalm-param R $value
     */
    public function __construct(int|float|bool|string|object $value)
    {
        $this->value = $value;
    }

    /**
     * @template LI
     * @template RI
     * @psalm-param RI $value
     * @psalm-return self<LI, RI>
     */
    public static function of(int|float|bool|string|object $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-return R
     */
    public function get(): int|float|bool|string|object
    {
        return $this->value;
    }
}
