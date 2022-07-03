<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

/**
 * @template E
 * @extends Validated<E, never>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class Invalid extends Validated
{
    /**
     * @param E $value
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * @return E
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
