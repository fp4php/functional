<?php

declare(strict_types=1);

namespace Fp\Functional;

class Unit
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpureMethodCall, ImpureStaticProperty
     */
    public static function getInstance(): self
    {
        return is_null(self::$instance)
            ? self::$instance = new self()
            : self::$instance;
    }
}
