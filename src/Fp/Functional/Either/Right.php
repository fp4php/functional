<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant R
 * @extends Either<never, R>
 */
final class Right extends Either
{
    /**
     * @param R $value
     */
    public function __construct(
        private readonly mixed $value,
    ) {}

    /**
     * @return R
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
