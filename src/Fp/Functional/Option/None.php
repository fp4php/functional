<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @psalm-immutable
 * @extends Option<empty>
 */
final class None extends Option
{
    private static ?self $instance = null;

    /**
     * @psalm-var null
     */
    protected mixed $value = null;

    private function __construct()
    {
    }

    /**
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty
     */
    public static function getInstance(): self
    {
        return is_null(self::$instance)
            ? self::$instance = new self()
            : self::$instance;
    }

    /**
     * @psalm-return null
     */
    public function get(): mixed
    {
        return $this->value;
    }
}
