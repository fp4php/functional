<?php

declare(strict_types=1);

namespace Fp\Functional\Option;

/**
 * @extends Option<never>
 * @psalm-suppress InvalidTemplateParam
 */
final class None extends Option
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        return is_null(self::$instance) ? self::$instance = new self() : self::$instance;
    }

    /**
     * @return null
     */
    public function get(): mixed
    {
        return null;
    }
}
