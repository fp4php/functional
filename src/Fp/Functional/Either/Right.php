<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant R
 * @extends Either<never, R>
 *
 * @psalm-suppress InvalidTemplateParam
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
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(never): LO $ifLeft
     * @param callable(R): RO $ifRight
     * @return RO|LO
     */
    public function fold(callable $ifLeft, callable $ifRight): mixed
    {
        return $ifRight($this->value);
    }
}
