<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Collection\at;
use function Fp\Collection\copyCollection;
use function Fp\Collection\every;
use function Fp\Collection\filter;
use function Fp\Collection\first;
use function Fp\Collection\flatMap;
use function Fp\Collection\fold;
use function Fp\Collection\group;
use function Fp\Collection\head;
use function Fp\Collection\last;
use function Fp\Collection\map;
use function Fp\Collection\partition;
use function Fp\Collection\pluck;
use function Fp\Collection\pop;
use function Fp\Collection\reduce;
use function Fp\Collection\reverse;
use function Fp\Collection\second;
use function Fp\Collection\shift;
use function Fp\Collection\some;
use function Fp\Collection\tail;

final class EveryTest extends TestCase
{
    public function testAt(): void
    {
        $this->assertTrue(at(['a' => true], 'a')->get());
    }

    public function testCopyCollection(): void
    {
        $c = ['a' => 1, 'b' => 2];
        $this->assertEquals($c, copyCollection($c));
    }

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

    public function testFilter(): void
    {
        $c = [1, 2];

        $this->assertEquals([1], filter(
            $c,
            fn(int $v) => $v < 2
        ));
    }

    public function testFirst(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(1, first($c)->get());
        $this->assertEquals(
            2,
            first($c, fn(int $v, int $k) => $k === 1)->get()
        );
    }

    public function testFlatMap(): void
    {
        $c = [1, 4];

        $this->assertEquals(
            [0, 1, 2, 3, 4, 5],
            flatMap(
                $c,
                fn(int $v) => [$v - 1, $v, $v + 1]
            )
        );
    }

    public function testFold(): void
    {
        $c = ['a', 'b', 'c'];

        $this->assertEquals(
            'abc',
            fold(
                '',
                $c,
                fn(string $acc, string $v) => $acc . $v
            )
        );
    }

    public function testGroup(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            ['y' => ['a' => 1, 'c' => 3], 'x' => ['b' => 2, 'd' => 4]],
            group(
                $c,
                fn(int $v, string $k) => ($v % 2 === 0) ? 'x' : 'y'
            )
        );
    }

    public function testHead(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(
            1,
            head($c)->get()
        );
    }

    public function testLast(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(
            3,
            last($c)->get()
        );
    }

    public function testMap(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals(
            ['a' => '2', 'b' => '3', 'c' => '4'],
            map(
                $c,
                fn(int $v) => (string) ($v + 1)
            )
        );
    }

    public function testPartition(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];

        $this->assertEquals(
            [['b' => 2, 'd' => 4], ['a' => 1, 'c' => 3]],
            partition(
                $c,
                fn(int $v) => $v % 2 === 0
            )
        );

        $this->assertEquals(
            [['a' => 1], ['b' => 2], ['c' => 3], ['d' => 4], []],
            partition(
                $c,
                fn(int $v) => $v === 1,
                fn(int $v) => $v === 2,
                fn(int $v) => $v === 3,
                fn(int $v) => $v === 4,
            )
        );
    }

    public function testPluck(): void
    {
        $this->assertEquals(
            [1, 2],
            pluck(
                [['a' => 1], ['a' => 2]],
                'a'
            )
        );

        $this->assertEquals(
            [1, 3],
            pluck(
                [new Foo(1), new Foo(3)],
                'a'
            )
        );
    }

    public function testPop(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals(
            [3, [1, 2]],
            pop($c)->get()?->toArray()
        );
    }

    public function testReduce(): void
    {
        $c = ['a', 'b', 'c'];

        $this->assertEquals(
            'abc',
            reduce($c, fn(string $acc, string $v) => $acc . $v)->get()
        );
    }

    public function testReverse(): void
    {
        $this->assertEquals(
            ['b', 'a'],
            reverse(['a', 'b'])
        );

        $this->assertEquals(
            ['k1' => 'b', 'k2' => 'a'],
            reverse(['k2' => 'a', 'k1' => 'b'])
        );
    }

    public function testSecond(): void
    {
        $this->assertEquals(
            'b',
            second(['a', 'b', 'c'])->get()
        );
    }

    public function testShift(): void
    {
        $c = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->assertEquals(
            [1, [2, 3]],
            shift($c)->get()?->toArray()
        );
    }

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

    public function testTail(): void
    {
        $this->assertEquals([1 => 2, 2 => 3, 3 => 4], tail([1, 2, 3, 4]));
    }
}
