<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

/**
 * @template A
 * @psalm-immutable
 * @extends Validated<empty, A>
 */
final class Valid extends Validated
{
    /**
     * @psalm-param A $value
     */
    public function __construct(protected mixed $value)
    {
    }

    /**
     * @psalm-return A
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
