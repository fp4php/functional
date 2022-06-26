<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySeq;

use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptySeq;
use Fp\Functional\Option\Option;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\SubBar;

final class NonEmptySeqOpsTest extends TestCase
{
    public function provideAppendAndPrependData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    public function testGroupMapReduce(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                10 => [10, 15, 20],
                20 => [10, 15],
                30 => [20],
            ]),
            NonEmptyArrayList::collectNonEmpty([
                ['id' => 10, 'sum' => 10],
                ['id' => 10, 'sum' => 15],
                ['id' => 10, 'sum' => 20],
                ['id' => 20, 'sum' => 10],
                ['id' => 20, 'sum' => 15],
                ['id' => 30, 'sum' => 20],
            ])->groupMapReduce(
                fn(array $a) => $a['id'],
                fn(array $a) => [$a['sum']],
                fn(array $old, array $new) => array_merge($old, $new),
            )
        );

        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                10 => [10, 15, 20],
                20 => [10, 15],
                30 => [20],
            ]),
            NonEmptyLinkedList::collectNonEmpty([
                ['id' => 10, 'sum' => 10],
                ['id' => 10, 'sum' => 15],
                ['id' => 10, 'sum' => 20],
                ['id' => 20, 'sum' => 10],
                ['id' => 20, 'sum' => 15],
                ['id' => 30, 'sum' => 20],
            ])->groupMapReduce(
                fn(array $a) => $a['id'],
                fn(array $a) => [$a['sum']],
                fn(array $old, array $new) => array_merge($old, $new),
            )
        );
    }

    /**
     * @dataProvider provideAppendAndPrependData
     */
    public function testAppendAndPrepend(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            [-2, -1, 0, 1, 2, 3, 4, 5, 6],
            $seq->prepended(0)
                ->appended(4)
                ->appendedAll([5, 6])
                ->prependedAll([-2, -1])
                ->toList(),
        );
    }

    public function provideTestAtData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([0, 1, 2, 3, 4, 5])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([0, 1, 2, 3, 4, 5])];
    }

    /**
     * @dataProvider provideTestAtData
     */
    public function testAt(NonEmptySeq $seq): void
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
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([0, 1, 2, 3, 4, 5])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([0, 1, 2, 3, 4, 5])];
    }

    /**
     * @dataProvider provideTestEveryData
     */
    public function testEvery(NonEmptySeq $seq): void
    {
        $this->assertTrue($seq->every(fn($i) => $i >= 0));
        $this->assertFalse($seq->every(fn($i) => $i > 0));
    }

    public function provideTestEveryOfData(): Generator
    {
        yield NonEmptyArrayList::class => [
            NonEmptyArrayList::collectNonEmpty([new Foo(1), new Foo(1)]),
            NonEmptyArrayList::collectNonEmpty([new Bar(true), new Foo(1)]),
        ];
        yield NonEmptyLinkedList::class => [
            NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Foo(1)]),
            NonEmptyLinkedList::collectNonEmpty([new Bar(true), new Foo(1)]),
        ];
    }

    /**
     * @dataProvider provideTestEveryOfData
     */
    public function testEveryOf(NonEmptySeq $seq1, NonEmptySeq $seq2): void
    {
        $this->assertTrue($seq1->everyOf(Foo::class));
        $this->assertFalse($seq2->everyOf(Foo::class));
    }

    public function provideTestExistsData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([new Foo(1), 1, new Foo(1)])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([new Foo(1), 1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestEveryMapData
     */
    public function testEveryMap(NonEmptySeq $seq1, NonEmptySeq $seq2): void
    {
        $this->assertEquals(
            Option::some($seq1),
            $seq1->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
        $this->assertEquals(
            Option::none(),
            $seq2->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
    }

    public function provideTestEveryMapData(): Generator
    {
        yield NonEmptyArrayList::class => [
            NonEmptyArrayList::collectNonEmpty([1, 2, 3]),
            NonEmptyArrayList::collectNonEmpty([0, 1, 2]),
        ];
        yield NonEmptyLinkedList::class => [
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3]),
            NonEmptyLinkedList::collectNonEmpty([0, 1, 2]),
        ];
    }

    /**
     * @dataProvider provideTestExistsData
     */
    public function testExists(NonEmptySeq $seq): void
    {
        $this->assertTrue($seq->exists(fn($i) => $i === 1));
        $this->assertFalse($seq->exists(fn($i) => $i === 2));
    }

    public function provideTestExistsOfData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, new Foo(1)])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestExistsOfData
     */
    public function testExistsOf(NonEmptySeq $seq): void
    {
        $this->assertTrue($seq->existsOf(Foo::class));
        $this->assertFalse($seq->existsOf(Bar::class));
    }

    public function provideTestFilterData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([new Foo(1), 1, new Foo(1)])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([new Foo(1), 1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestFilterData
     */
    public function testFilter(NonEmptySeq $seq): void
    {
        $this->assertEquals([1], $seq->filter(fn($i) => $i === 1)->toList());
    }

    public function provideTestFilterMapData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty(['zero', '1', '2'])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty(['zero', '1', '2'])];
    }

    /**
     * @dataProvider provideTestFilterMapData
     */
    public function testFilterMap(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            [1, 2],
            $seq->filterMap(fn($e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
                ->toList()
        );
    }

    public function provideTestFilterNotNullData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, null, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, null, 3])];
    }

    /**
     * @dataProvider provideTestFilterNotNullData
     */
    public function testFilterNotNull(NonEmptySeq $seq): void
    {
        $this->assertEquals([1, 3], $seq->filterNotNull()->toList());
    }

    public function provideTestFilterOfData(): Generator
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);

        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([new Foo(1), $bar, $subBar]), $bar, $subBar];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([new Foo(1), $bar, $subBar]), $bar, $subBar];
    }

    /**
     * @dataProvider provideTestFilterOfData
     */
    public function testFilterOf(NonEmptySeq $seq, Bar $bar, SubBar $subBar): void
    {
        $this->assertEquals([$bar, $subBar], $seq->filterOf(Bar::class, false)->toList());
        $this->assertEquals([$bar], $seq->filterOf(Bar::class, true)->toList());
    }

    public function provideTestFirstData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([new Foo(1), 2, 1, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([new Foo(1), 2, 1, 3])];
    }

    /**
     * @dataProvider provideTestFirstData
     */
    public function testFirst(NonEmptySeq $seq): void
    {
        $this->assertEquals(1, $seq->first(fn($e) => 1 === $e)->get());
        $this->assertNull($seq->first(fn($e) => 5 === $e)->get());
    }

    public function provideTestFirstOfAndLastOfData(): Generator
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);

        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([new Foo(1), $subBar, $bar]), $bar, $subBar];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([new Foo(1), $subBar, $bar]), $bar, $subBar];
    }

    /**
     * @dataProvider provideTestFirstOfAndLastOfData
     */
    public function testFirstOfAndLastOf(NonEmptySeq $seq, Bar $bar, SubBar $subBar): void
    {
        $this->assertEquals($subBar, $seq->firstOf(Bar::class, false)->get());
        $this->assertEquals($bar, $seq->firstOf(Bar::class, true)->get());

        $this->assertEquals($bar, $seq->lastOf(Bar::class, false)->get());
        $this->assertEquals($subBar, $seq->lastOf(SubBar::class, true)->get());
    }

    public function provideTestFlatMapData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([2, 5])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([2, 5])];
    }

    /**
     * @dataProvider provideTestFlatMapData
     * @param NonEmptySeq<int> $seq
     */
    public function testFlatMap(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            $seq->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList()
        );
    }

    public function provideTestHeadData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([2, 5])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([2, 5])];
    }

    /**
     * @dataProvider provideTestHeadData
     */
    public function testHead(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            2,
            $seq->head()
        );
    }

    public function provideTestLastData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([2, 3, 0])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([2, 3, 0])];
    }

    /**
     * @dataProvider provideTestLastData
     */
    public function testLast(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            3,
            $seq->last(fn($e) => $e > 0)->get()
        );
    }

    public function provideTestFirstAndLastElementData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestFirstAndLastElementData
     */
    public function testFirstAndLastElement(NonEmptySeq $seq): void
    {
        $this->assertEquals(1, $seq->firstElement());
        $this->assertEquals(3, $seq->lastElement());
    }

    public function provideTestMapData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestMapData
     * @param NonEmptySeq<int> $seq
     */
    public function testMap(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            $seq->map(fn($e) => (string) ($e + 1))->toList()
        );
    }

    public function provideTestMapWithKeyData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestMapWithKeyData
     * @param NonEmptySeq<int> $seq
     */
    public function testMapKV(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            ['0-1', '1-2', '2-3'],
            $seq->mapKV(fn($key, $elem) => "{$key}-{$elem}")->toList()
        );
    }

    public function provideTestReduceData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty(['1', '2', '3'])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestReduceData
     * @param NonEmptySeq<string> $seq
     */
    public function testReduce(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            '123',
            $seq->reduce(fn(string $acc, $e) => $acc . $e)
        );
    }

    public function provideTestReverseData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty(['1', '2', '3'])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestReverseData
     */
    public function testReverse(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            ['3', '2', '1'],
            $seq->reverse()->toList()
        );
    }

    public function provideTestTailData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty(['1', '2', '3'])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestTailData
     */
    public function testTail(NonEmptySeq $seq): void
    {
        $this->assertEquals(['2', '3'], $seq->tail()->toList());
    }

    public function provideTestUniqueData(): Generator
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);

        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([$foo1, $foo1, $foo2]), $foo1, $foo2];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([$foo1, $foo1, $foo2]), $foo1, $foo2];
    }

    /**
     * @dataProvider provideTestUniqueData
     */
    public function testUnique(NonEmptySeq $seq, Foo $foo1, Foo $foo2): void
    {
        $this->assertEquals(
            [$foo1, $foo2],
            $seq->unique(fn(Foo $e) => $e->a)->toList()
        );
    }

    public function provideTestGroupByData(): Generator
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);
        $foo3 = new Foo(1);
        $foo4 = new Foo(3);

        yield NonEmptyArrayList::class => [
            NonEmptyArrayList::collectNonEmpty([$foo1, $foo2, $foo3, $foo4]),
            $foo1,
            $foo2,
            $foo3,
            $foo4
        ];
        yield NonEmptyLinkedList::class => [
            NonEmptyLinkedList::collectNonEmpty([$foo1, $foo2, $foo3, $foo4]),
            $foo1,
            $foo2,
            $foo3,
            $foo4
        ];
    }

    /**
     * @param NonEmptySeq<Foo> $seq
     * @dataProvider provideTestGroupByData
     */
    public function testGroupBy(NonEmptySeq $seq, Foo $f1, Foo $f2, Foo $f3, Foo $f4): void
    {
        $res1 = $seq->groupBy(fn(Foo $foo) => $foo)
            ->map(fn($entry) => $entry->toList())
            ->toList();

        $res2 = $seq->groupBy(fn(Foo $foo) => $foo->a)
            ->map(fn($entry) => $entry->toList())
            ->toList();

        $res3 = $seq->map(fn(Foo $foo) => $foo->a)
            ->groupBy(fn(int $a) => $a)
            ->map(fn($entry) => $entry->toList())
            ->toList();

        $this->assertEquals([[$f1, [$f1, $f3]], [$f2, [$f2]], [$f4, [$f4]]], $res1);
        $this->assertEquals([[1, [$f1, $f3]], [2, [$f2]], [3, [$f4]]], $res2);
        $this->assertEquals([[1, [1, 1]], [2, [2]], [3, [3]]], $res3);
    }

    public function provideTestTapData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([new Foo(1), new Foo(2)])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Foo(2)])];
    }

    /**
     * @dataProvider provideTestTapData
     */
    public function testTap(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            [2, 3],
            $seq->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->toList()
        );
    }

    public function provideTestSortedData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestSortedData
     * @param NonEmptySeq<int> $seq
     */
    public function testSorted(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            [1, 2, 3],
            $seq->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toList()
        );

        $this->assertEquals(
            [3, 2, 1],
            $seq->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toList()
        );
    }

    public function provideTestTakeAndDropData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([0, 1, 2])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([0, 1, 2])];
    }

    /**
     * @dataProvider provideTestTakeAndDropData
     */
    public function testTakeAndDrop(NonEmptySeq $seq): void
    {
        $this->assertEquals([0, 1], $seq->takeWhile(fn($e) => $e < 2)->toList());
        $this->assertEquals([2], $seq->dropWhile(fn($e) => $e < 2)->toList());
        $this->assertEquals([0, 1], $seq->take(2)->toList());
        $this->assertEquals([2], $seq->drop(2)->toList());
    }

    public function testArrayListReindex(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectPairsNonEmpty([
                ['key-1', 1],
                ['key-2', 2],
                ['key-3', 3],
            ]),
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])
                ->reindex(fn($value) => "key-{$value}"),
        );

        $this->assertEquals(
            NonEmptyHashMap::collectPairsNonEmpty([
                ['key-01', 1],
                ['key-12', 2],
                ['key-23', 3],
            ]),
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])
                ->reindexKV(fn($key, $value) => "key-{$key}{$value}"),
        );
    }

    public function testLinkedListReindex(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectPairsNonEmpty([
                ['key-1', 1],
                ['key-2', 2],
                ['key-3', 3],
            ]),
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])
                ->reindex(fn($value) => "key-{$value}"),
        );

        $this->assertEquals(
            NonEmptyHashMap::collectPairsNonEmpty([
                ['key-01', 1],
                ['key-12', 2],
                ['key-23', 3],
            ]),
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])
                ->reindexKV(fn($key, $value) => "key-{$key}{$value}"),
        );
    }
}
