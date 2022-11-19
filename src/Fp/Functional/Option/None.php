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
     * {@inheritDoc}
     *
     * @template SO
     * @template NO
     *
     * @param callable(): NO $ifNone
     * @param callable(never): SO $ifSome
     * @return SO|NO
     */
    public function fold(callable $ifNone, callable $ifSome): mixed
    {
        return $ifNone();
    }
}
