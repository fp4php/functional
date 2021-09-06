<?php

declare(strict_types=1);

namespace Fp\Functional;

use Fp\Collections\PureThunk;

class Unit
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    /**
     * @psalm-pure
     */
    public static function getInstance(): self
    {
        return PureThunk::of(function () {
            return is_null(self::$instance)
                ? self::$instance = new self()
                : self::$instance;
        })();
    }
}
