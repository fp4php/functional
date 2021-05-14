<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\filter;
use function Fp\Collection\filterInstancesOf;
use function Fp\Collection\filterNotNull;

final class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $c = [1, 2];

        $this->assertEquals([1], filter(
            $c,
            fn(int $v) => $v < 2
        ));
    }

    public function testFilterNotNull(): void
    {
        $this->assertEquals(
            [0 => 1, 1 => 2, 2 => 3, 4 => 4, 5 => 5, 7 => 6],
            filterNotNull([1, 2, 3, null, 4, 5, null, 6], true)
        );

        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            filterNotNull([1, 2, 3, null, 4, 5, null, 6], false)
        );
    }

    public function testFilterInstancesOf(): void
    {
        $foo = new Foo(1);
        $bar = new Bar(true);

        $this->assertEquals(
            [2 => $bar],
            filterInstancesOf([1, $foo, $bar, 4], Bar::class)
        );

        $this->assertEquals(
            ['2' => $bar],
            filterInstancesOf([1, $foo, $bar, 4], Bar::class)
        );

        $this->assertEquals(
            [$bar],
            filterInstancesOf([1, $foo, $bar, 4], Bar::class, false)
        );
    }
}
