<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant L
 * @psalm-immutable
 * @extends Either<L, empty>
 */
final class Left extends Either
{
    /**
     * @psalm-param L $value
     */
    public function __construct(protected int|float|bool|string|object|array $value) {}

    /**
     * @template LI
     * @psalm-param LI $value
     * @psalm-return self<LI>
     * @psalm-pure
     */
    public static function of(int|float|bool|string|object|array $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-return L
     */
    public function get(): int|float|bool|string|object|array
    {
        return $this->value;
    }
}
