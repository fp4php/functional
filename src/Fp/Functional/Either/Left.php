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
    public function __construct(protected mixed $value) {}

    /**
     * @psalm-return L
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
