<?php

declare(strict_types=1);

namespace Fp\Functional\Validated;

/**
 * @template A
 * @extends Validated<never, A>
 *
 * @psalm-suppress InvalidTemplateParam
 */
final class Valid extends Validated
{
    /**
     * @param A $value
     */
    public function __construct(private mixed $value)
    {
    }

    /**
     * @return A
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
