<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Collection\every;
use function Fp\Collection\everyKV;
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

    public function testEveryKV(): void
    {
        /** @var array<int, int> */
        $c1 = [
            2 => 3,
            4 => 5,
            6 => 7,
        ];

        $this->assertTrue(everyKV($c1, fn($k, $v) => $k % 2 === 0 && $v % 2 !== 0));

        /** @var array<int, int> */
        $c2 = [
            3 => 2,
            5 => 4,
            7 => 6,
        ];

        $this->assertFalse(everyKV($c2, fn($k, $v) => $k % 2 === 0 && $v % 2 !== 0));
    }

    public function testEveryOf(): void
    {
        $this->assertTrue(everyOf(
            [],
            Foo::class,
            false
        ));

        $this->assertTrue(everyOf(
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
