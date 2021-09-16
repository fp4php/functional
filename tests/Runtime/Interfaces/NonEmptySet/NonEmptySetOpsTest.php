<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySet;

use Fp\Collections\HashSet;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptySet;
use Fp\Functional\Option\Option;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\SubBar;

final class NonEmptySetOpsTest extends TestCase
{
    public function provideTestUpdatedAndRemovedData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 2])];
    }

    /**
     * @dataProvider provideTestUpdatedAndRemovedData
     */
    public function testUpdatedAndRemoved(NonEmptySet $set): void
    {
        $this->assertEquals(
            [2, 3],
            $set->updated(3)->removed(1)->toArray()
        );
    }

    public function provideTestContainsData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 2])];
    }

    /**
     * @dataProvider provideTestContainsData
     */
    public function testContains(NonEmptySet $set): void
    {
        $this->assertTrue($set->contains(1));
        $this->assertTrue($set->contains(2));
        $this->assertFalse($set->contains(3));

        $this->assertTrue($set(1));
        $this->assertTrue($set(2));
        $this->assertFalse($set(3));
    }

    public function provideTestEveryData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([0, 1, 2, 3, 4, 5])];
    }

    /**
     * @dataProvider provideTestEveryData
     */
    public function testEvery(NonEmptySet $set): void
    {
        $this->assertTrue($set->every(fn($i) => $i >= 0));
        $this->assertFalse($set->every(fn($i) => $i > 0));
    }

    public function provideTestEveryOfData(): Generator
    {
        yield NonEmptyHashSet::class => [
            NonEmptyHashSet::collectNonEmpty([new Foo(1), new Foo(1)]),
            NonEmptyHashSet::collectNonEmpty([new Bar(true), new Foo(1)]),
        ];
    }

    /**
     * @dataProvider provideTestEveryOfData
     */
    public function testEveryOf(NonEmptySet $set1, NonEmptySet $set2): void
    {
        $this->assertTrue($set1->everyOf(Foo::class));
        $this->assertFalse($set2->everyOf(Foo::class));
    }

    public function provideTestExistsData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([new Foo(1), 1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestExistsData
     */
    public function testExists(NonEmptySet $set): void
    {
        $this->assertTrue($set->exists(fn($i) => $i === 1));
        $this->assertFalse($set->exists(fn($i) => $i === 2));
    }

    public function provideTestExistsOfData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestExistsOfData
     */
    public function testExistsOf(NonEmptySet $set): void
    {
        $this->assertTrue($set->existsOf(Foo::class));
        $this->assertFalse($set->existsOf(Bar::class));
    }

    public function provideTestFilterData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([new Foo(1), 1, new Foo(1)])];
    }

    /**
     * @dataProvider provideTestFilterData
     */
    public function testFilter(NonEmptySet $set): void
    {
        $this->assertEquals([1], $set->filter(fn($i) => $i === 1)->toArray());
    }

    public function provideTestFilterMapData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty(['zero', '1', '2'])];
    }

    /**
     * @dataProvider provideTestFilterMapData
     */
    public function testFilterMap(NonEmptySet $set): void
    {
        $this->assertEquals(
            [1, 2],
            $set->filterMap(fn($e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
                ->toArray()
        );
    }

    public function provideTestFilterNotNullData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, null, 3])];
    }

    /**
     * @dataProvider provideTestFilterNotNullData
     */
    public function testFilterNotNull(NonEmptySet $set): void
    {
        $this->assertEquals([1, 3], $set->filterNotNull()->toArray());
    }

    public function provideTestFilterOfData(): Generator
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);

        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([new Foo(1), $bar, $subBar]), $bar, $subBar];
    }

    /**
     * @dataProvider provideTestFilterOfData
     */
    public function testFilterOf(NonEmptySet $set, Bar $bar, SubBar $subBar): void
    {
        $this->assertEquals([$bar, $subBar], $set->filterOf(Bar::class, false)->toArray());
        $this->assertEquals([$bar], $set->filterOf(Bar::class, true)->toArray());
    }

    public function provideTestFirstData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([new Foo(1), 2, 1, 3])];
    }

    /**
     * @dataProvider provideTestFirstData
     */
    public function testFirst(NonEmptySet $set): void
    {
        $this->assertEquals(1, $set->first(fn($e) => 1 === $e)->get());
        $this->assertNull($set->first(fn($e) => 5 === $e)->get());
    }

    public function provideTestFirstOfData(): Generator
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);

        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([new Foo(1), $subBar, $bar]), $bar, $subBar];
    }

    /**
     * @dataProvider provideTestFirstOfData
     */
    public function testFirstOf(NonEmptySet $set, Bar $bar, SubBar $subBar): void
    {
        $this->assertEquals($subBar, $set->firstOf(Bar::class, false)->get());
        $this->assertEquals($bar, $set->firstOf(Bar::class, true)->get());
    }

    public function provideTestFlatMapData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([2, 5])];
    }

    /**
     * @dataProvider provideTestFlatMapData
     * @param NonEmptySet<int> $set
     */
    public function testFlatMap(NonEmptySet $set): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            $set->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
        );
    }

    public function provideTestHeadData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([2, 5])];
    }

    /**
     * @dataProvider provideTestHeadData
     */
    public function testHead(NonEmptySet $set): void
    {
        $this->assertEquals(
            2,
            $set->head()
        );
    }

    public function provideTestLastData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([2, 3, 0])];
    }

    /**
     * @dataProvider provideTestLastData
     */
    public function testLast(NonEmptySet $set): void
    {
        $this->assertEquals(
            3,
            $set->last(fn($e) => $e > 0)->get()
        );
    }

    public function provideTestFirstAndLastElementData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestFirstAndLastElementData
     */
    public function testFirstAndLastElement(NonEmptySet $set): void
    {
        $this->assertEquals(1, $set->firstElement());
        $this->assertEquals(3, $set->lastElement());
    }

    public function provideTestMapData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 3])];
    }

    /**
     * @dataProvider provideTestMapData
     * @param NonEmptySet<int> $set
     */
    public function testMap(NonEmptySet $set): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            $set->map(fn($e) => (string) ($e + 1))->toArray()
        );
    }

    public function provideTestReduceData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestReduceData
     * @param NonEmptySet<string> $set
     */
    public function testReduce(NonEmptySet $set): void
    {
        $this->assertEquals(
            '123',
            $set->reduce(fn(string $acc, $e) => $acc . $e)
        );
    }

    public function provideTestReverseData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty(['1', '2', '3'])];
    }

    public function provideTestTailData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty(['1', '2', '3'])];
    }

    /**
     * @dataProvider provideTestTailData
     */
    public function testTail(NonEmptySet $set): void
    {
        $this->assertEquals(['2', '3'], $set->tail()->toArray());
    }

    public function provideTestUniqueData(): Generator
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);

        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([$foo1, $foo1, $foo2]), $foo1, $foo2];
    }

    public function provideTestGroupByData(): Generator
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);
        $foo3 = new Foo(1);
        $foo4 = new Foo(3);

        yield NonEmptyHashSet::class => [
            NonEmptyHashSet::collectNonEmpty([$foo1, $foo2, $foo3, $foo4]),
            $foo1,
            $foo2,
            $foo3,
            $foo4
        ];
    }

    public function provideTestTapData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([new Foo(1), new Foo(2)])];
    }

    /**
     * @dataProvider provideTestTapData
     */
    public function testTap(NonEmptySet $set): void
    {
        $this->assertEquals(
            [2, 3],
            $set->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->toArray()
        );
    }

    public function provideTestSubsetOfData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2])];
    }

    /**
     * @dataProvider provideTestSubsetOfData
     */
    public function testSubsetOf(NonEmptySet $set): void
    {
        $this->assertFalse($set->subsetOf(HashSet::collect([])));
        $this->assertTrue($set->subsetOf($set));
        $this->assertTrue($set->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2, 3])));
        $this->assertFalse(NonEmptyHashSet::collectNonEmpty([1, 2, 3])->subsetOf($set));
    }
}
