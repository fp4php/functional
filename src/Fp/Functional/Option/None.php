<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @extends Option<never>
 */
final class None extends Option
{
    private static ?None $instance = null;

    public static function getInstance(): None
    {
        return null === self::$instance ? self::$instance = new None() : self::$instance;
    }

    /**
     * @return null
     */
    public function get(): mixed
    {
        return null;
    }
}
