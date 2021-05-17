<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @psalm-immutable
 * @extends Option<empty>
 */
final class None extends Option
{
    /**
     * @psalm-var null
     */
    protected mixed $value = null;

    /**
     * @psalm-return null
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
