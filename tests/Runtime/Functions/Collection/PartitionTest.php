<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\partition;
use function Fp\Collection\partitionOf;

final class PartitionTest extends TestCase
{
    public function testPartition(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            [[2, 4], [1, 3]],
            partition(
                $c,
                fn(int $v) => $v % 2 === 0
            )
        );

        $this->assertEquals(
            [[1], [2], [3], [4], []],
            partition(
                $c,
                fn(int $v) => $v === 1,
                fn(int $v) => $v === 2,
                fn(int $v) => $v === 3,
                fn(int $v) => $v === 4,
            )
        );
    }

    public function testPartitionOf(): void
    {
        $foo = new Foo(1);
        $bar = new Bar(true);

        $this->assertEquals(
            [[], []],
            partitionOf(
                [],
                true,
                Foo::class
            )
        );

        $this->assertEquals(
            [[$foo], [$bar]],
            partitionOf(
                [$foo, $bar],
                true,
                Foo::class
            )
        );

        $this->assertEquals(
            [[$foo, $foo], []],
            partitionOf(
                [$foo, $foo],
                true,
                Foo::class
            )
        );

        $this->assertEquals(
            [[$foo, $foo], [$bar], [1]],
            partitionOf(
                [$foo, $foo, $bar, 1],
                true,
                Foo::class,
                Bar::class
            )
        );
    }
}
