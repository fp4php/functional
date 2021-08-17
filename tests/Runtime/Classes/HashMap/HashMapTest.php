<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashMap;

use Fp\Collections\HashMap;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class HashMapTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collect([['a', 1], ['b', 2]])->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collectIterable(['a' => 1, 'b' => 2])->toArray(),
        );
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collect([['a', 1], ['b', 2]])->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collect([['a', 1], ['b', 2]])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            HashMap::collect([['a', 1], ['b', 2]])->toHashSet()->toArray(),
        );
    }

    public function testContract(): void
    {
        $bar = new Bar(1);
        $hm = HashMap::collect([[$bar, 'v1'], [new Foo(2), 'v2']]);
        $hm1 = HashMap::collect([[[new Foo(1), new Foo(2)], 'v1']]);

        $this->assertEquals(
            'v2',
            $hm(new Foo(2))->get(),
        );

        $this->assertEquals(
            'v1',
            $hm1([new Foo(1), new Foo(2)])->get(),
        );

        $this->assertEquals(
            'v1',
            $hm($bar)->get(),
        );

        $this->assertNull($hm(new Bar(1))->get());
    }

    public function testCollisions(): void
    {
        $hm = HashMap::collect([[1, 'v1'], [true, 'v2'], ['1', 'v3']]);
        $hm1 = HashMap::collect([[0, 'v1'], [false, 'v2'], ['', 'v3']]);

        $this->assertEquals('v1', $hm(1)->get());
        $this->assertEquals('v2', $hm(true)->get());
        $this->assertEquals('v3', $hm('1')->get());

        $this->assertEquals('v1', $hm1(0)->get());
        $this->assertEquals('v2', $hm1(false)->get());
        $this->assertEquals('v3', $hm1('')->get());
    }
}
