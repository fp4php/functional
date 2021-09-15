<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\partition;
use function Fp\Collection\partitionOf;

final class PartitionStaticTest
{
    /**
     * @return array{0: list<2|3|4|5>, 1: list<2|3|4|5>}
     */
    public function testPartitionWithOnePredicate(): array
    {
        return partition(
            [2, 3, 4, 5],
            fn(int $v) => $v % 2 === 0
        );
    }

    /**
     * @return array{0: list<1>, 1: list<1>, 2: list<1>}
     */
    public function testPartitionWithTwoPredicates(): array
    {
        return partition(
            [1],
            fn(int $v) => $v % 2 === 0,
            fn(int $v) => $v % 2 === 1,
        );
    }

    /**
     * @return array{0:list<Foo>, 1:list<Bar|Foo>}
     */
    public function testPartitionOfWithOneClass(): array
    {
        return partitionOf(
            [new Foo(1), new Bar(true)],
            false,
            Foo::class
        );
    }

    /**
     * @return array{0:list<Foo>, 1:list<Bar>, 2:list<Foo>, 3:list<Bar|Foo>}
     */
    public function testPartitionOfWithThreeClasses(): array
    {
        return partitionOf(
            [new Foo(1), new Bar(true)],
            false,
            Foo::class,
            Bar::class,
            Foo::class,
        );
    }

    /**
     * @return array{
     *     0:list<Foo>,
     *     1:list<Bar>,
     *     2:list<Foo>,
     *     3:list<Bar>,
     *     4:list<Foo>,
     *     5:list<Bar>,
     *     6:list<Foo>,
     *     7:list<Bar>,
     *     8:list<Foo>,
     *     9:list<Bar>,
     *     10:list<Bar|Foo>
     * }
     */
    public function testPartitionOfWithTenClasses(): array
    {
        return partitionOf(
            [new Foo(1), new Bar(true)],
            false,
            Foo::class,
            Bar::class,
            Foo::class,
            Bar::class,
            Foo::class,
            Bar::class,
            Foo::class,
            Bar::class,
            Foo::class,
            Bar::class,
        );
    }
}
