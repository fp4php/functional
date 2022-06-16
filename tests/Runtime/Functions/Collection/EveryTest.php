<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Foo;

use function Fp\Collection\every;
use function Fp\Collection\everyMap;
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

    public function testEveryMap(): void
    {
        /** @var list<int> $c */
        $c = [1, 2];

        $this->assertEquals(
            Option::some($c),
            everyMap($c, fn(int $v) => $v < 3 ? Option::some($v) : Option::none())
        );

        $this->assertEquals(
            Option::none(),
            everyMap($c, fn(int $v) => $v < 2 ? Option::some($v) : Option::none()),
        );
    }
}
