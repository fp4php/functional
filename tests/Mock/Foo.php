<?php

declare(strict_types=1);

namespace Tests\Mock;

/**
 * @internal
 */
class Foo {
    public function __construct(public int $a)
    {
    }

    public static function test(int $a, bool $b): string
    {
        return 'x' . $a;
    }
}
