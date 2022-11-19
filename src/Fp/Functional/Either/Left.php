<?php

declare(strict_types=1);

namespace Fp\Functional\Either;

/**
 * @template-covariant L
 * @extends Either<L, never>
 *
 * @psalm-suppress InvalidTemplateParam
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
     * {@inheritDoc}
     *
     * @template LO
     * @template RO
     *
     * @param callable(L): LO $ifLeft
     * @param callable(never): RO $ifRight
     * @return RO|LO
     */
    public function fold(callable $ifLeft, callable $ifRight): mixed
    {
        return $ifLeft($this->value);
    }
}
