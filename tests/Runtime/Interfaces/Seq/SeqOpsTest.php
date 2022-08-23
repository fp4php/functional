<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\Seq;
use Fp\Functional\Option\Option;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\SubBar;

final class SeqOpsTest extends TestCase
{
    public function provideAppendAndPrependData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3])];
    }

    /**
     * @dataProvider provideAppendAndPrependData
     */
    public function testAppendAndPrepend(Seq $seq): void
    {
        $this->assertEquals(
            [-2, -1, 0, 1, 2, 3, 4, 5, 6],
            $seq->prepended(0)
                ->appended(4)
                ->appendedAll([5, 6])
                ->prependedAll([-2, -1])
                ->toArray(),
        );
    }

    public function provideTestAtData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([0, 1, 2, 3, 4, 5])];
        yield LinkedList::class => [LinkedList::collect([0, 1, 2, 3, 4, 5])];
    }

    /**
     * @dataProvider provideTestAtData
     */
    public function testAt(Seq $seq): void
    {
        $this->assertEquals(0, $seq->at(0)->getUnsafe());
        $this->assertEquals(3, $seq->at(3)->getUnsafe());
        $this->assertEquals(5, $seq->at(5)->getUnsafe());
        $this->assertEquals(0, $seq(0)->getUnsafe());
        $this->assertEquals(3, $seq(3)->getUnsafe());
        $this->assertEquals(5, $seq(5)->getUnsafe());
    }

    public function provideTestEveryData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([0, 1, 2, 3, 4, 5])];
        yield LinkedList::class => [LinkedList::collect([0, 1, 2, 3, 4, 5])];
    }

    /**
     * @dataProvider provideTestEveryData
     */
    public function testEvery(Seq $seq): void
    {
        $this->assertTrue($seq->every(fn($i) => $i >= 0));
        $this->assertFalse($seq->every(fn($i) => $i > 0));
    }

    public function provideTestEveryOfData(): Generator
    {
        yield ArrayList::class => [
            ArrayList::collect([new Foo(1), new Foo(1)]),
            ArrayList::collect([new Bar(true), new Foo(1)]),
        ];
        yield LinkedList::class => [
            LinkedList::collect([new Foo(1), new Foo(1)]),
            LinkedList::collect([new Bar(true), new Foo(1)]),
        ];
    }

    /**
     * @dataProvider provideTestEveryOfData
     */
    public function testEveryOf(Seq $seq1, Seq $seq2): void
    {
        $this->assertTrue($seq1->everyOf(Foo::class));
        $this->assertFalse($seq2->everyOf(Foo::class));
    }

    public function provideTestExistsData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([new Foo(1), 1, new Foo(1)])];
        yield LinkedList::class => [LinkedList::collect([new Foo(1), 1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestEveryMapData
     */
    public function testEveryMap(Seq $seq1, Seq $seq2): void
    {
        $this->assertEquals(
            Option::some($seq1),
            $seq1->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
        $this->assertEquals(
            Option::none(),
            $seq2->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
    }

    public function provideTestEveryMapData(): Generator
    {
        yield ArrayList::class => [
            ArrayList::collect([1, 2, 3]),
            ArrayList::collect([0, 1, 2]),
        ];
        yield LinkedList::class => [
            LinkedList::collect([1, 2, 3]),
            LinkedList::collect([0, 1, 2]),
        ];
    }

    /**
     * @dataProvider provideTestExistsData
     */
    public function testExists(Seq $seq): void
    {
        $this->assertTrue($seq->exists(fn($i) => $i === 1));
        $this->assertFalse($seq->exists(fn($i) => $i === 2));
    }

    public function provideTestExistsOfData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, new Foo(1)])];
        yield LinkedList::class => [LinkedList::collect([1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestExistsOfData
     */
    public function testExistsOf(Seq $seq): void
    {
        $this->assertTrue($seq->existsOf(Foo::class));
        $this->assertFalse($seq->existsOf(Bar::class));
    }

    public function provideTestFilterData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([new Foo(1), 1, new Foo(1)])];
        yield LinkedList::class => [LinkedList::collect([new Foo(1), 1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestFilterData
     */
    public function testFilter(Seq $seq): void
    {
        $this->assertEquals([1], $seq->filter(fn($i) => $i === 1)->toArray());
    }

    public function provideTestFilterMapData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect(['zero', '1', '2'])];
        yield LinkedList::class => [LinkedList::collect(['zero', '1', '2'])];
    }

    /**
     * @dataProvider provideTestFilterMapData
     */
    public function testFilterMap(Seq $seq): void
    {
        $this->assertEquals(
            [1, 2],
            $seq->filterMap(fn($e) => is_numeric($e) ? Option::some((int)$e) : Option::none())
                ->toArray()
        );
    }

    public function provideTestFilterNotNullData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, null, 3])];
        yield LinkedList::class => [LinkedList::collect([1, null, 3])];
    }

    /**
     * @dataProvider provideTestFilterNotNullData
     */
    public function testFilterNotNull(Seq $seq): void
    {
        $this->assertEquals([1, 3], $seq->filterNotNull()->toArray());
    }

    public function provideTestFilterOfData(): Generator
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);

        yield ArrayList::class => [ArrayList::collect([new Foo(1), $bar, $subBar]), $bar, $subBar];
        yield LinkedList::class => [LinkedList::collect([new Foo(1), $bar, $subBar]), $bar, $subBar];
    }

    /**
     * @dataProvider provideTestFilterOfData
     */
    public function testFilterOf(Seq $seq, Bar $bar, SubBar $subBar): void
    {
        $this->assertEquals([$bar, $subBar], $seq->filterOf(Bar::class, false)->toArray());
        $this->assertEquals([$bar], $seq->filterOf(Bar::class, true)->toArray());
    }

    public function provideTestFirstData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([new Foo(1), 2, 1, 3])];
        yield LinkedList::class => [LinkedList::collect([new Foo(1), 2, 1, 3])];
    }

    /**
     * @dataProvider provideTestFirstData
     */
    public function testFirst(Seq $seq): void
    {
        $this->assertEquals(1, $seq->first(fn($e) => 1 === $e)->get());
        $this->assertNull($seq->first(fn($e) => 5 === $e)->get());
    }

    public function provideTestFirstOfAndLastOfData(): Generator
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);

        yield ArrayList::class => [ArrayList::collect([new Foo(1), $subBar, $bar]), $bar, $subBar];
        yield LinkedList::class => [LinkedList::collect([new Foo(1), $subBar, $bar]), $bar, $subBar];
    }

    /**
     * @dataProvider provideTestFirstOfAndLastOfData
     */
    public function testFirstOfAndLastOf(Seq $seq, Bar $bar, SubBar $subBar): void
    {
        $this->assertEquals($subBar, $seq->firstOf(Bar::class, false)->get());
        $this->assertEquals($bar, $seq->firstOf(Bar::class, true)->get());

        $this->assertEquals($bar, $seq->lastOf(Bar::class, false)->get());
        $this->assertEquals($subBar, $seq->lastOf(SubBar::class, true)->get());
    }

    public function provideTestFlatMapData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([2, 5])];
        yield LinkedList::class => [LinkedList::collect([2, 5])];
    }

    /**
     * @dataProvider provideTestFlatMapData
     * @param Seq<int> $seq
     */
    public function testFlatMap(Seq $seq): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            $seq->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
        );
    }

    public function provideTestHeadData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([2, 5])];
        yield LinkedList::class => [LinkedList::collect([2, 5])];
    }

    /**
     * @dataProvider provideTestHeadData
     */
    public function testHead(Seq $seq): void
    {
        $this->assertEquals(
            2,
            $seq->head()->get()
        );
    }

    public function provideTestLastData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([2, 3, 0])];
        yield LinkedList::class => [LinkedList::collect([2, 3, 0])];
    }

    /**
     * @dataProvider provideTestLastData
     */
    public function testLast(Seq $seq): void
    {
        $this->assertEquals(
            3,
            $seq->last(fn($e) => $e > 0)->get()
        );
    }

    public function provideTestFirstAndLastElementData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestFirstAndLastElementData
     */
    public function testFirstAndLastElement(Seq $seq): void
    {
        $this->assertEquals(1, $seq->firstElement()->get());
        $this->assertEquals(3, $seq->lastElement()->get());
    }

    public function provideTestMapData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestMapData
     * @param Seq<int> $seq
     */
    public function testMap(Seq $seq): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            $seq->map(fn($e) => (string)($e + 1))->toArray()
        );
    }

    public function provideTestReduceData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect(['1', '2', '3'])];
        yield LinkedList::class => [LinkedList::collect(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestReduceData
     * @param Seq<string> $seq
     */
    public function testReduce(Seq $seq): void
    {
        $this->assertEquals(
            '123',
            $seq->reduce(fn(string $acc, $e) => $acc . $e)->get()
        );
    }

    public function provideTestFoldData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect(['1', '2', '3'])];
        yield LinkedList::class => [LinkedList::collect(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestReduceData
     * @param Seq<string> $seq
     */
    public function testFold(Seq $seq): void
    {
        $this->assertEquals(
            '123',
            $seq->fold('', fn(string $acc, $e) => $acc . $e)
        );
    }

    public function provideTestReverseData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect(['1', '2', '3'])];
        yield LinkedList::class => [LinkedList::collect(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestReverseData
     */
    public function testReverse(Seq $seq): void
    {
        $this->assertEquals(
            ['3', '2', '1'],
            $seq->reverse()->toArray()
        );
    }

    public function provideTestTailData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect(['1', '2', '3'])];
        yield LinkedList::class => [LinkedList::collect(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestTailData
     */
    public function testTail(Seq $seq): void
    {
        $this->assertEquals(['2', '3'], $seq->tail()->toArray());
    }

    public function provideTestUniqueData(): Generator
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);

        yield ArrayList::class => [ArrayList::collect([$foo1, $foo1, $foo2]), $foo1, $foo2];
        yield LinkedList::class => [LinkedList::collect([$foo1, $foo1, $foo2]), $foo1, $foo2];
    }

    /**
     * @dataProvider provideTestUniqueData
     */
    public function testUnique(Seq $seq, Foo $foo1, Foo $foo2): void
    {
        $this->assertEquals(
            [$foo1, $foo2],
            $seq->unique(fn(Foo $e) => $e->a)->toArray()
        );
    }

    public function provideTestGroupByData(): Generator
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);
        $foo3 = new Foo(1);
        $foo4 = new Foo(3);

        yield ArrayList::class => [
            ArrayList::collect([$foo1, $foo2, $foo3, $foo4]),
            $foo1,
            $foo2,
            $foo3,
            $foo4
        ];
        yield LinkedList::class => [
            LinkedList::collect([$foo1, $foo2, $foo3, $foo4]),
            $foo1,
            $foo2,
            $foo3,
            $foo4
        ];
    }

    /**
     * @dataProvider provideTestGroupByData
     */
    public function testGroupBy(Seq $seq, Foo $f1, Foo $f2, Foo $f3, Foo $f4): void
    {
        $res1 = $seq->groupBy(fn(Foo $foo) => $foo)
            ->map(fn($entry) => $entry->value->toArray())
            ->toArray();

        $res2 = $seq->groupBy(fn(Foo $foo) => $foo->a)
            ->map(fn($entry) => $entry->value->toArray())
            ->toArray();

        $res3 = $seq->map(fn(Foo $foo) => $foo->a)
            ->groupBy(fn(int $a) => $a)
            ->map(fn($entry) => $entry->value->toArray())
            ->toArray();

        $this->assertEquals([[$f1, [$f1, $f3]], [$f2, [$f2]], [$f4, [$f4]]], $res1);
        $this->assertEquals([[1, [$f1, $f3]], [2, [$f2]], [3, [$f4]]], $res2);
        $this->assertEquals([[1, [1, 1]], [2, [2]], [3, [3]]], $res3);
    }

    public function provideTestTapData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([new Foo(1), new Foo(2)])];
        yield LinkedList::class => [LinkedList::collect([new Foo(1), new Foo(2)])];
    }

    /**
     * @dataProvider provideTestTapData
     */
    public function testTap(Seq $seq): void
    {
        $this->assertEquals(
            [2, 3],
            $seq->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->toArray()
        );
    }

    public function provideTestSortedData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestSortedData
     * @param Seq<int> $seq
     */
    public function testSorted(Seq $seq): void
    {
        $this->assertEquals(
            [1, 2, 3],
            $seq->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toArray()
        );

        $this->assertEquals(
            [3, 2, 1],
            $seq->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toArray()
        );
    }

    public function provideTestIsEmptyData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3]), ArrayList::collect([])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3]), LinkedList::collect([])];
    }

    /**
     * @dataProvider provideTestIsEmptyData
     */
    public function testIsEmpty(Seq $seq1, Seq $seq2): void
    {
        $this->assertFalse($seq1->isEmpty());
        $this->assertTrue($seq1->isNonEmpty());
        $this->assertTrue($seq2->isEmpty());
        $this->assertFalse($seq2->isNonEmpty());
    }

    public function provideTestTakeAndDropData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([0, 1, 2])];
        yield LinkedList::class => [LinkedList::collect([0, 1, 2])];
    }

    /**
     * @dataProvider provideTestTakeAndDropData
     */
    public function testTakeAndDrop(Seq $seq): void
    {
        $this->assertEquals([0, 1], $seq->takeWhile(fn($e) => $e < 2)->toArray());
        $this->assertEquals([2], $seq->dropWhile(fn($e) => $e < 2)->toArray());
        $this->assertEquals([0, 1], $seq->take(2)->toArray());
        $this->assertEquals([2], $seq->drop(2)->toArray());
    }

    public function provideTestIntersperseData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([0, 1, 2])];
        yield LinkedList::class => [LinkedList::collect([0, 1, 2])];
    }

    /**
     * @dataProvider provideTestIntersperseData
     */
    public function testIntersperse(Seq $seq): void
    {
        $this->assertEquals([0, ',', 1, ',', 2], $seq->intersperse(',')->toArray());
    }

    public function provideTestZipData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([0, 1, 2])];
        yield LinkedList::class => [LinkedList::collect([0, 1, 2])];
    }

    /**
     * @dataProvider provideTestZipData
     */
    public function testZip(Seq $seq): void
    {
        $this->assertEquals([[0, 'a'], [1, 'b']], $seq->zip(['a', 'b'])->toArray());
    }

    public function provideTestMkString(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([0, 1, 2]), ArrayList::empty()];
        yield LinkedList::class => [LinkedList::collect([0, 1, 2]), LinkedList::empty()];
    }

    /**
     * @dataProvider provideTestMkString
     */
    public function testMkString(Seq $seq, Seq $emptySeq): void
    {
        $this->assertEquals('(0,1,2)', $seq->mkString('(', ',', ')'));
        $this->assertEquals('()', $emptySeq->mkString('(', ',', ')'));
    }

    public function provideTestMaxNotEmptyCollections(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([3, 7, 2]), Option::some(7)];
        yield LinkedList::class => [LinkedList::collect([9, 1, 2]), Option::some(9)];
    }

    /**
     * @dataProvider provideTestMaxNotEmptyCollections
     */
    public function testMaxInNotEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->max());
    }

    public function provideTestMaxEmptyCollections(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([]), Option::none()];
        yield LinkedList::class => [LinkedList::collect([]), Option::none()];
    }

    /**
     * @dataProvider provideTestMaxEmptyCollections
     */
    public function testMaxInEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->max());
    }

    public function provideTestMaxByNotEmptyCollections(): Generator
    {
        yield ArrayList::class => [
            ArrayList::collect([new Foo(1), new Foo(5), new Foo(2)]),
            Option::some(new Foo(5))
        ];
        yield LinkedList::class => [
            LinkedList::collect([new Foo(9), new Foo(1), new Foo(2)]),
            Option::some(new Foo(9))
        ];
    }

    /**
     * @param Seq<Foo> $seq
     * @param Option<Foo> $option
     * @dataProvider provideTestMaxByNotEmptyCollections
     */
    public function testMaxByInNotEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->maxBy(fn(Foo $foo) => $foo->a));
    }

    public function provideTestMaxByEmptyCollections(): Generator
    {
        yield ArrayList::class => [
            ArrayList::collect([]),
            Option::none()
        ];
        yield LinkedList::class => [
            LinkedList::collect([]),
            Option::none()
        ];
    }

    /**
     * @param Seq<Foo> $seq
     * @param Option<Foo> $option
     * @dataProvider provideTestMaxByEmptyCollections
     */
    public function testMaxByInEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->maxBy(fn(Foo $foo) => $foo->a));
    }

    public function provideTestMinNotEmptyCollections(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([3, 7, 2]), Option::some(2)];
        yield LinkedList::class => [LinkedList::collect([9, 1, 2]), Option::some(1)];
    }

    /**
     * @dataProvider provideTestMinNotEmptyCollections
     */
    public function testMinInNotEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->min());
    }

    public function provideTestMinEmptyCollections(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([]), Option::none()];
        yield LinkedList::class => [LinkedList::collect([]), Option::none()];
    }

    /**
     * @dataProvider provideTestMinEmptyCollections
     */
    public function testMinInEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->min());
    }

    public function provideTestMinByNotEmptyCollections(): Generator
    {
        yield ArrayList::class => [
            ArrayList::collect([new Foo(1), new Foo(5), new Foo(2)]),
            Option::some(new Foo(1))
        ];
        yield LinkedList::class => [
            LinkedList::collect([new Foo(9), new Foo(4), new Foo(2)]),
            Option::some(new Foo(2))
        ];
    }

    /**
     * @param Seq<Foo> $seq
     * @param Option<Foo> $option
     * @dataProvider provideTestMinByNotEmptyCollections
     */
    public function testMinByInNotEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->minBy(fn(Foo $foo) => $foo->a));
    }

    public function provideTestMinByEmptyCollections(): Generator
    {
        yield ArrayList::class => [
            ArrayList::collect([]),
            Option::none()
        ];
        yield LinkedList::class => [
            LinkedList::collect([]),
            Option::none()
        ];
    }

    /**
     * @param Seq<Foo> $seq
     * @param Option<Foo> $option
     * @dataProvider provideTestMinByEmptyCollections
     */
    public function testMinByInEmptyCollection(Seq $seq, Option $option): void
    {
        $this->assertEquals($option, $seq->minBy(fn(Foo $foo) => $foo->a));
    }
}
