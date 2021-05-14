<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\some;
use function Fp\Collection\someOf;

final class SomeTest extends TestCase
{
    public function testSome(): void
    {
        $this->assertTrue(some(
            [1, 2],
            fn(int $v) => $v < 2
        ));

        $this->assertFalse(some(
            [2, 3 ,4],
            fn(int $v) => $v < 2
        ));
    }

    public function testSomeOf(): void
    {
        $this->assertTrue(someOf(
            [1, new Foo(1)],
            Foo::class
        ));

        $this->assertFalse(someOf(
            [1, new Foo(1)],
            Bar::class
        ));
    }
}
