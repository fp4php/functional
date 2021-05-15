<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\any;
use function Fp\Collection\anyOf;

final class AnyTest extends TestCase
{
    public function testAny(): void
    {
        $this->assertTrue(any(
            [1, 2],
            fn(int $v) => $v < 2
        ));

        $this->assertFalse(any(
            [2, 3 ,4],
            fn(int $v) => $v < 2
        ));
    }

    public function testAnyOf(): void
    {
        $this->assertTrue(anyOf(
            [1, new Foo(1)],
            Foo::class
        ));

        $this->assertFalse(anyOf(
            [1, new Foo(1)],
            Bar::class
        ));
    }
}
