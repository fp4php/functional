<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use Tests\Mock\Foo;

use function Fp\Collection\every;
use function Fp\Collection\everyOf;

final class EveryTest extends TestCase
{
    public function testEvery(): void
    {
        $c = [1, 2];

        $this->assertTrue(every(
            $c,
            fn(int $v) => $v < 3
        ));

        $this->assertFalse(every(
            $c,
            fn(int $v) => $v < 2
        ));
    }

    public function testEveryOf(): void
    {
        $this->assertTrue(everyOf(
            [],
            Foo::class,
            false
        ));

        $this->assertFalse(everyOf(
            [],
            Foo::class,
            true
        ));

        $this->assertTrue(everyOf(
            [new Foo(1), new Foo(2)],
            Foo::class
        ));

        $this->assertFalse(everyOf(
            [new Foo(1), new Foo(2), 1],
            Foo::class
        ));
    }
}
