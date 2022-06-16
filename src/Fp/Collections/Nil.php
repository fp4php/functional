<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-suppress InvalidTemplateParam
 * @extends LinkedList<empty>
 */
final class Nil extends LinkedList
{
    private static ?self $instance = null;

    /**
     * @psalm-suppress ImpureStaticProperty
     */
    public static function getInstance(): self
    {
        return is_null(self::$instance) ? self::$instance = new self() : self::$instance;
    }
}
