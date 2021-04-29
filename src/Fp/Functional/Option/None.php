<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @psalm-immutable
 */
final class None extends Option
{
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @psalm-return null
     */
    public function get(): mixed
    {
        return null;
    }
}
