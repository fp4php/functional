<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant L
 * @extends Either<L, never>
 */
final class Left extends Either
{
    /**
     * @param L $value
     */
    public function __construct(
        private readonly mixed $value,
    ) {}

    /**
     * @psalm-return L
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
