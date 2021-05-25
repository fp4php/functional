<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant R
 * @psalm-immutable
 * @extends Either<empty, R>
 */
final class Right extends Either
{
    /**
     * @psalm-param R $value
     */
    public function __construct(protected mixed $value) {}

    /**
     * @template RI
     * @psalm-param RI $value
     * @psalm-return self<RI>
     * @psalm-pure
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @psalm-return R
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
