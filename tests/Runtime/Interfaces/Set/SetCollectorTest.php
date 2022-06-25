<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Set;

use Fp\Collections\HashSet;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class SetCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 1, 2, 3, 3])->toList(),
        );

        $this->assertCount(
            1,
            HashSet::collect([new Foo(1), new Foo(1)])->toList()
        );

        $this->assertCount(
            2,
            HashSet::collect([new Bar(1), new Bar(1)])->toList()
        );

        $this->assertCount(
            1,
            HashSet::collect([[new Foo(1), new Foo(2)], [new Foo(1), new Foo(2)]])->toList()
        );

        $this->assertCount(
            2,
            HashSet::collect([[new Foo(1), new Bar(2)], [new Foo(1), new Bar(2)]])->toList()
        );

        $bar1 = new Bar(1);
        $bar2 = $bar1;

        $this->assertCount(
            1,
            HashSet::collect([$bar1, $bar2])->toList()
        );
    }
}
