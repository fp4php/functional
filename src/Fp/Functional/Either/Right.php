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
    public function __construct(private mixed $value) {}

    /**
     * @return R
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
