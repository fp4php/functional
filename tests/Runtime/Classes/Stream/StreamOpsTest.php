<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Stream;

use Fp\Collections\HashMap;
use Fp\Collections\Seq;
use Fp\Streams\Stream;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Baz;
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

    public function testEveryN(): void
    {
        $this->assertTrue(Stream::emits([[1, 1], [2, 2], [3, 3]])->everyN(fn(int $a, int $b) => ($a + $b) <= 6));
        $this->assertFalse(Stream::emits([[1, 1], [2, 2], [3, 3]])->everyN(fn(int $a, int $b) => ($a + $b) < 6));
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

    public function testExistsN(): void
    {
        $this->assertTrue(Stream::emits([[1, 1], [2, 2], [3, 3]])->existsN(fn(int $a, int $b) => ($a + $b) === 6));
        $this->assertFalse(Stream::emits([[1, 1], [2, 2], [3, 3]])->existsN(fn(int $a, int $b) => ($a + $b) === 7));
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

        $this->assertEquals([1], $hs->filter(fn($i) => $i === 1)->toList());
        $this->assertEquals([1], Stream::emits([1, null])->filterNotNull()->toList());
    }

    public function testFilterN(): void
    {
        $actual = Stream::emits([[1, 1], [2, 2], [3, 3]])
            ->filterN(fn(int $a, int $b) => $a + $b >= 6)
            ->toList();

        $this->assertEquals([[3, 3]], $actual);
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
                ->toList()
        );
    }

    public function testFilterMapN(): void
    {
        $actual = Stream::emits([[1, 1], [2, 2], [3, 3]])
            ->filterMapN(fn(int $a, int $b) => Option::when($a + $b >= 6, fn() => $a))
            ->toList();

        $this->assertEquals([3], $actual);
    }

    public function testFirst(): void
    {
        $this->assertEquals(Option::some('1'), Stream::emits(['1', 2, '3'])->first(is_string(...)));
        $this->assertEquals(Option::none(), Stream::emits([])->first(is_string(...)));
    }

    public function testFirstElement(): void
    {
        $this->assertEquals(Option::some('1'), Stream::emits(['1', 2, '3'])->firstElement());
        $this->assertEquals(Option::none(), Stream::emits([])->firstElement());
    }

    public function testFirstOf(): void
    {
        $this->assertEquals(Option::some(new Foo(1)), Stream::emits([new Foo(1), 2, new Foo(2)])->firstOf(Foo::class));
        $this->assertEquals(Option::none(), Stream::emits([])->firstOf(Baz::class));
    }

    public function testFirstN(): void
    {
        $this->assertEquals(
            Option::some([2, 2, 'm3']),
            Stream::emits([[1, 1, 'm1'], [1, 1, 'm2'], [2, 2, 'm3'], [2, 2, 'm4']])
                ->firstN(fn(int $a, int $b) => ($a + $b) === 4),
        );
    }

    public function testLast(): void
    {
        $this->assertEquals(Option::some('3'), Stream::emits(['1', 2, '3'])->last(is_string(...)));
        $this->assertEquals(Option::none(), Stream::emits([])->last(is_string(...)));
    }

    public function testLastElement(): void
    {
        $this->assertEquals(Option::some('3'), Stream::emits(['1', 2, '3'])->lastElement());
        $this->assertEquals(Option::none(), Stream::emits([])->lastElement());
    }

    public function testLastOf(): void
    {
        $this->assertEquals(Option::some(new Foo(2)), Stream::emits([new Foo(1), 2, new Foo(2)])->lastOf(Foo::class));
        $this->assertEquals(Option::none(), Stream::emits([])->lastOf(Foo::class));
    }

    public function testLastN(): void
    {
        $this->assertEquals(
            Option::some([2, 2, 'm4']),
            Stream::emits([[1, 1, 'm1'], [1, 1, 'm2'], [2, 2, 'm3'], [2, 2, 'm4']])
                ->lastN(fn(int $a, int $b) => ($a + $b) === 4),
        );
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            Stream::emits([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList()
        );
    }

    public function testFlatMapN(): void
    {
        $this->assertEquals(
            [2, 3, 4, 5, 6, 7],
            Stream::emits([[1, 2], [3, 4], [5, 6]])
                ->flatMapN(fn(int $a, int $b) => [$a + 1, $b + 1])
                ->toList(),
        );
    }

    public function testFold(): void
    {
        $this->assertEquals(6, Stream::emits([2, 3])->fold(1)(fn(int $acc, $e) => $acc + $e));
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            Stream::emits([1, 2, 3])
                ->map(fn($e) => (string) ($e + 1))
                ->toList(),
        );
    }

    public function testMapN(): void
    {
        $this->assertEquals(
            ['2', '4', '6'],
            Stream::emits([[1, 1], [2, 2], [3, 3]])
                ->mapN(fn(int $a, int $b) => (string) ($a + $b))
                ->toList(),
        );
    }

    public function testTap(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([new Foo(1), new Foo(2)])
                ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->toList()
        );
    }

    public function testTapN(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([[new Foo(1), 2], [new Foo(2), 3]])
                ->tapN(fn(Foo $foo, int $new) => $foo->a = $new)
                ->mapN(fn(Foo $foo) => $foo->a)
                ->toList(),
        );
    }

    public function testRepeat(): void
    {
        $this->assertEquals([1, 2, 1, 2, 1], Stream::emits([1,2])->repeat()->take(5)->toList());
        $this->assertEquals([1], Stream::emit(1)->repeat(1)->toList());
        $this->assertEquals([1, 1, 1], Stream::emit(1)->repeat(3)->toList());
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
                ->toList()
        );
    }

    public function testTail(): void
    {
        $this->assertEquals(
            [2, 3],
            Stream::emits([1, 2, 3])->tail()->toList()
        );
    }

    public function testTakeAndDrop(): void
    {
        $this->assertEquals([0, 1], Stream::emits([0, 1, 2])->takeWhile(fn($e) => $e < 2)->toList());
        $this->assertEquals([2], Stream::emits([0, 1, 2])->dropWhile(fn($e) => $e < 2)->toList());
        $this->assertEquals([0, 1], Stream::emits([0, 1, 2])->take(2)->toList());
        $this->assertEquals([2], Stream::emits([0, 1, 2])->drop(2)->toList());
    }

    public function testIntersperse(): void
    {
        $this->assertEquals([0, '.', 1, '.', 2], Stream::emits([0, 1, 2])->intersperse('.')->toList());
        $this->assertEquals([], Stream::emits([])->intersperse('.')->toList());
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
            Stream::emits([0, 1, 2])->interleave(['a', 'b'])->toList()
        );

        $this->assertEquals(
            [0, 'a', 1, 'b'],
            Stream::emits([0, 1])->interleave(['a', 'b', 'c'])->toList()
        );
    }

    public function testZip(): void
    {
        $this->assertEquals(
            [[0, 'a'], [1, 'b']],
            Stream::emits([0, 1, 2])->zip(['a', 'b'])->toList()
        );

        $this->assertEquals(
            [[0, 'a'], [1, 'b']],
            Stream::emits([0, 1])->zip(['a', 'b', 'c'])->toList()
        );
    }

    public function testChunks(): void
    {
        $this->assertEquals(
            [[1, 2], [3, 4], [5]],
            Stream::emits([1, 2, 3, 4, 5])
                ->chunks(2)
                ->map(fn(Seq $seq) => $seq->toList())
                ->toList()
        );
    }

    public function testGroupAdjacentBy(): void
    {
        $this->assertEquals(
            [["H", ["Hello", "Hi"]], ["G", ["Greetings"]], ["H", ["Hey"]]],
            Stream::emits(["Hello", "Hi", "Greetings", "Hey"])
                ->groupAdjacentBy(fn($str) => $str[0])
                ->map(fn($pair) => [$pair[0], $pair[1]->toList()])
                ->toList()
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

    public function testReindex(): void
    {
        $this->assertEquals(
            HashMap::collectPairs([
                ['key-1', 1],
                ['key-2', 2],
                ['key-3', 3],
            ]),
            Stream::emits([1, 2, 3])
                ->reindex(fn($value) => "key-{$value}"),
        );
    }

    public function testReindexN(): void
    {
        $this->assertEquals(
            HashMap::collectPairs([
                ['x-1', ['x', 1]],
                ['y-2', ['y', 2]],
                ['z-3', ['z', 3]],
            ]),
            Stream::emits([['x', 1], ['y', 2], ['z', 3]])
                ->reindexN(fn(string $a, int $b) => "{$a}-{$b}"),
        );
    }
}
