<?php

declare(strict_types=1);

namespace Fp\Functional;

final class Unit
{
    private static ?Unit $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): Unit
    {
        return null === self::$instance
            ? self::$instance = new Unit()
            : self::$instance;
    }
}
