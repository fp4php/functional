<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashSet;

use Fp\Collections\HashSet;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class HashSetTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 1, 2, 3, 3])->toArray(),
        );

        $this->assertCount(
            1,
            HashSet::collect([new Foo(1), new Foo(1)])->toArray()
        );

        $this->assertCount(
            2,
            HashSet::collect([new Bar(1), new Bar(1)])->toArray()
        );

        $this->assertCount(
            1,
            HashSet::collect([[new Foo(1), new Foo(2)], [new Foo(1), new Foo(2)]])->toArray()
        );

        $this->assertCount(
            2,
            HashSet::collect([[new Foo(1), new Bar(2)], [new Foo(1), new Bar(2)]])->toArray()
        );

        $bar1 = new Bar(1);
        $bar2 = $bar1;

        $this->assertCount(
            1,
            HashSet::collect([$bar1, $bar2])->toArray()
        );
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toHashSet()->toArray(),
        );
    }
}
