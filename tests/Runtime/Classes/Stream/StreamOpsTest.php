<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Stream;

use Fp\Collections\Seq;
use Fp\Streams\Stream;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class StreamOpsTest extends TestCase
{
    public function testEvery(): void
    {
        $every = Stream::emits([0, 1, 2, 3, 4, 5]);
        $some = Stream::emits([0, 1, 2, 3, 4, 5]);

        $this->assertTrue($every->every(fn($i) => $i >= 0));
        $this->assertFalse($some->every(fn($i) => $i > 0));
    }

    public function testEveryOf(): void
    {
        $this->assertTrue(Stream::emits([new Foo(1), new Foo(2)])->everyOf(Foo::class));
        $this->assertFalse(Stream::emits([new Foo(1), new Bar(2)])->everyOf(Foo::class));
    }

    public function testExists(): void
    {
        /** @psalm-var Stream<object|scalar> $hasOne */
        $hasOne = Stream::emits([new Foo(1), 1, 1, new Foo(1)]);

        /** @psalm-var Stream<object|scalar> $hasNotTwo */
        $hasNotTwo = Stream::emits([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hasOne->exists(fn($i) => $i === 1));
        $this->assertFalse($hasNotTwo->exists(fn($i) => $i === 2));
    }

    public function testExistsOf(): void
    {
        $hasFoo = Stream::emits([new Foo(1), 1, 1, new Foo(1)]);
        $hasNotFoo = Stream::emits([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hasFoo->existsOf(Foo::class));
        $this->assertFalse($hasNotFoo->existsOf(Bar::class));
    }

    public function testFilter(): void
    {
        $hs = Stream::emits([new Foo(1), 1, new Foo(1)]);

        $this->assertEquals([1], $hs->filter(fn($i) => $i === 1)->toArray());
        $this->assertEquals([1], Stream::emits([1, null])->filterNotNull()->toArray());
    }

    public function testFilterOf(): void
    {
        $hs = Stream::emits([new Foo(1), 1, 2, new Foo(1)]);
        $this->assertCount(2, $hs->filterOf(Foo::class));
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [1, 2],
            Stream::emits(['zero', '1', '2'])
                ->filterMap(fn($e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
                ->toArray()
        );
    }

    public function testFirstsAndLasts(): void
    {
        $this->assertEquals('1', Stream::emits(['1', 2, '3'])->first(fn($i) => is_string($i))->get());
        $this->assertEquals('1', Stream::emits(['1', 2, '3'])->firstElement()->get());

        $this->assertEquals('3', Stream::emits(['1', 2, '3'])->last(fn($i) => is_string($i))->get());
        $this->assertEquals('3', Stream::emits(['1', 2, '3'])->lastElement()->get());

        $s = Stream::emits([$f1 = new Foo(1), 2, new Foo(2)]);
        $this->assertEquals($f1, $s->firstOf(Foo::class)->get());
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            Stream::emits([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
        );
    }

    public function testFold(): void
    {
        /** @psalm-var Stream<int> $list */
        $list = Stream::emits([2, 3]);

        $this->assertEquals(
            6,
            $list->fold(1, fn(int $acc, $e) => $acc + $e)
        );
    }

    public function testReduce(): void
    {
        /** @psalm-var Stream<string> $list */
        $list = Stream::emits(['1', '2', '3']);

        $this->assertEquals(
            '123',
            $list->reduce(fn(string $acc, $e) => $acc . $e)->get()
        );
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            Stream::emits([1, 2, 3])->map(fn($e) => (string) ($e + 1))->toArray()
        );
    }

    public function testTap(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([new Foo(1), new Foo(2)])
                ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->toArray()
        );
    }

    public function testRepeat(): void
    {
        $this->assertEquals([1, 2, 1, 2, 1], Stream::emits([1,2])->repeat()->take(5)->toArray());
        $this->assertEquals([1], Stream::emit(1)->repeatN(1)->toArray());
        $this->assertEquals([1, 1, 1], Stream::emit(1)->repeatN(3)->toArray());
    }

    public function testAppendedAndPrepended(): void
    {
        $this->assertEquals(
            [-2, -1, 0, 1, 2, 3, 4, 5, 6],
            Stream::emits([1, 2, 3])
                ->prepended(0)
                ->appended(4)
                ->appendedAll([5, 6])
                ->prependedAll([-2, -1])
                ->toArray()
        );
    }

    public function testTail(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([1, 2, 3])->tail()->toArray()
        );
    }

    public function testTakeAndDrop(): void
    {
        $this->assertEquals([0, 1], Stream::emits([0, 1, 2])->takeWhile(fn($e) => $e < 2)->toArray());
        $this->assertEquals([2], Stream::emits([0, 1, 2])->dropWhile(fn($e) => $e < 2)->toArray());
        $this->assertEquals([0, 1], Stream::emits([0, 1, 2])->take(2)->toArray());
        $this->assertEquals([2], Stream::emits([0, 1, 2])->drop(2)->toArray());
    }

    public function testIntersperse(): void
    {
        $this->assertEquals([0, '.', 1, '.', 2], Stream::emits([0, 1, 2])->intersperse('.')->toArray());
        $this->assertEquals([], Stream::emits([])->intersperse('.')->toArray());
    }

    public function testLines(): void
    {
        Stream::emits([1, 2])->lines()->drain();
        $this->expectOutputString('12');
    }

    public function testInterleave(): void
    {
        $this->assertEquals(
            [0, 'a', 1, 'b'],
            Stream::emits([0, 1, 2])->interleave(['a', 'b'])->toArray()
        );

        $this->assertEquals(
            [0, 'a', 1, 'b'],
            Stream::emits([0, 1])->interleave(['a', 'b', 'c'])->toArray()
        );
    }

    public function testZip(): void
    {
        $this->assertEquals(
            [[0, 'a'], [1, 'b']],
            Stream::emits([0, 1, 2])->zip(['a', 'b'])->toArray()
        );

        $this->assertEquals(
            [[0, 'a'], [1, 'b']],
            Stream::emits([0, 1])->zip(['a', 'b', 'c'])->toArray()
        );
    }

    public function testChunks(): void
    {
        $this->assertEquals(
            [[1, 2], [3, 4], [5]],
            Stream::emits([1, 2, 3, 4, 5])
                ->chunks(2)
                ->map(fn(Seq $seq) => $seq->toArray())
                ->toArray()
        );
    }

    public function testGroupAdjacentBy(): void
    {
        $this->assertEquals(
            [["H", ["Hello", "Hi"]], ["G", ["Greetings"]], ["H", ["Hey"]]],
            Stream::emits(["Hello", "Hi", "Greetings", "Hey"])
                ->groupAdjacentBy(fn($str) => $str[0])
                ->map(fn($pair) => [$pair[0], $pair[1]->toArray()])
                ->toArray()
        );
    }

    public function testHead(): void
    {
        $this->assertEquals(
            1,
            Stream::emits([1, 2, 3])->head()->get()
        );
    }

    public function testMkString(): void
    {
        $this->assertEquals('(0,1,2)', Stream::emits([0, 1, 2])->mkString('(', ',', ')'));
        $this->assertEquals('()', Stream::emits([])->mkString('(', ',', ')'));
    }
}
