<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

/**
 * @template E
 * @psalm-immutable
 * @extends Validated<E, empty>
 */
final class Invalid extends Validated
{
    /**
     * @psalm-param E $value
     */
    public function __construct(protected mixed $value)
    {
    }

    /**
     * @psalm-return E
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
