<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\ArrayList;

use Fp\Collections\ArrayList;
use Fp\Collections\Seq;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\SubBar;

use function Fp\Cast\asList;

final class ArrayListOpsTest extends TestCase
{
    public function testAppendPrepend(): void
    {
        $linkedList = ArrayList::collect([1, 2, 3]);
        $linkedList = $linkedList
            ->prepended(0)
            ->appended(4)
            ->appendedAll([5, 6])
            ->prependedAll([-2, -1]);

        $list = asList($linkedList);

        $this->assertEquals(
            [-2, -1, 0, 1, 2, 3, 4, 5, 6],
            $list,
        );
    }

    public function testAt(): void
    {
        $linkedList = ArrayList::collect([0, 1, 2, 3, 4, 5]);

        $this->assertEquals(0, $linkedList->at(0)->getUnsafe());
        $this->assertEquals(3, $linkedList->at(3)->getUnsafe());
        $this->assertEquals(5, $linkedList->at(5)->getUnsafe());

        $this->assertEquals(0, $linkedList(0)->getUnsafe());
        $this->assertEquals(3, $linkedList(3)->getUnsafe());
        $this->assertEquals(5, $linkedList(5)->getUnsafe());
    }

    public function testEvery(): void
    {
        $linkedList = ArrayList::collect([0, 1, 2, 3, 4, 5]);

        $this->assertTrue($linkedList->every(fn($i) => $i >= 0));
        $this->assertFalse($linkedList->every(fn($i) => $i > 0));
    }

    public function testEveryOf(): void
    {
        $linkedList0 = ArrayList::collect([new Foo(1), new Foo(1)]);
        $linkedList1 = ArrayList::collect([new Bar(true), new Foo(1)]);

        $this->assertTrue($linkedList0->everyOf(Foo::class));
        $this->assertFalse($linkedList1->everyOf(Foo::class));
    }

    public function testExists(): void
    {
        /** @psalm-var ArrayList<mixed> $linkedList */
        $linkedList = ArrayList::collect([new Foo(1), 1, new Foo(1)]);

        $this->assertTrue($linkedList->exists(fn($i) => $i === 1));
        $this->assertFalse($linkedList->exists(fn($i) => $i === 2));
    }

    public function testExistsOf(): void
    {
        $linkedList = ArrayList::collect([1, new Foo(1)]);

        $this->assertTrue($linkedList->existsOf(Foo::class));
        $this->assertFalse($linkedList->existsOf(Bar::class));
    }

    public function testFilter(): void
    {
        $linkedList = ArrayList::collect([new Foo(1), 1, new Foo(1)]);
        $this->assertEquals([1], $linkedList->filter(fn($i) => $i === 1)->toArray());
    }

    public function testFilterNotNull(): void
    {
        $linkedList = ArrayList::collect([1, null, 3]);
        $this->assertEquals([1, 3], $linkedList->filterNotNull()->toArray());
    }

    public function testFilterOf(): void
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);
        $linkedList = ArrayList::collect([new Foo(1), $bar, $subBar]);

        $this->assertEquals([$bar, $subBar], $linkedList->filterOf(Bar::class, false)->toArray());
        $this->assertEquals([$bar], $linkedList->filterOf(Bar::class, true)->toArray());
    }

    public function testFirst(): void
    {
        /** @psalm-var ArrayList<mixed> $linkedList */
        $linkedList = ArrayList::collect([new Foo(1), 2, 1, 3]);

        $this->assertEquals(1, $linkedList->first(fn($e) => 1 === $e)->get());
        $this->assertNull($linkedList->first(fn($e) => 5 === $e)->get());
    }

    public function testFirstOf(): void
    {
        $bar = new Bar(1);
        $subBar = new SubBar(1);
        $linkedList = ArrayList::collect([new Foo(1), $subBar, $bar]);

        $this->assertEquals($subBar, $linkedList->firstOf(Bar::class, false)->get());
        $this->assertEquals($bar, $linkedList->firstOf(Bar::class, true)->get());
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            ArrayList::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
        );
    }

    public function testFold(): void
    {
        /** @psalm-var ArrayList<int> $list */
        $list = ArrayList::collect([2, 3]);

        $this->assertEquals(
            6,
            $list->fold(1, fn(int $acc, $e) => $acc + $e)
        );
    }

    public function testHead(): void
    {
        $this->assertEquals(
            2,
            ArrayList::collect([2, 3])->head()->get()
        );
    }

    public function testLast(): void
    {
        $this->assertEquals(
            3,
            ArrayList::collect([2, 3, 0])->last(fn($e) => $e > 0)->get()
        );
    }

    public function testFirstElement(): void
    {
        $this->assertEquals(
            1,
            ArrayList::collect([1, 2, 3])->firstElement()->get()
        );
    }

    public function testLastElement(): void
    {
        $this->assertEquals(
            0,
            ArrayList::collect([2, 3, 0])->lastElement()->get()
        );
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            ArrayList::collect([1, 2, 3])->map(fn($e) => (string) ($e + 1))->toArray()
        );
    }

    public function testReduce(): void
    {
        /** @psalm-var ArrayList<string> $list */
        $list = ArrayList::collect(['1', '2', '3']);

        $this->assertEquals(
            '123',
            $list->reduce(fn(string $acc, $e) => $acc . $e)->get()
        );
    }

    public function testReverse(): void
    {
        $this->assertEquals(
            [3, 2, 1],
            ArrayList::collect([1, 2, 3])->reverse()->toArray()
        );
    }

    public function testTail(): void
    {
        $this->assertEquals(
            [2, 3],
            ArrayList::collect([1, 2, 3])->tail()->toArray()
        );
    }

    public function testUnique(): void
    {
        $foo1 = new Foo(1);
        $foo2 = new Foo(2);
        $this->assertEquals(
            [$foo1, $foo2],
            ArrayList::collect([$foo1, $foo1, $foo2])->unique(fn(Foo $e) => $e->a)->toArray()
        );
    }

    public function testTakeAndDrop(): void
    {
        $this->assertEquals(
            [0, 1],
            ArrayList::collect([0, 1, 2])->takeWhile(fn($e) => $e < 2)->toArray()
        );

        $this->assertEquals(
            [2],
            ArrayList::collect([0, 1, 2])->dropWhile(fn($e) => $e < 2)->toArray()
        );

        $this->assertEquals(
            [0, 1],
            ArrayList::collect([0, 1, 2])->take(2)->toArray()
        );

        $this->assertEquals(
            [2],
            ArrayList::collect([0, 1, 2])->drop(2)->toArray()
        );
    }

    public function testGroupBy(): void
    {
        $foos = [
            $f1 = new Foo(1), $f2 = new Foo(2),
            $f3 = new Foo(1), $f4 = new Foo(3)
        ];

        $res1 = ArrayList::collect($foos)
            ->groupBy(fn(Foo $foo) => $foo)
            ->map(fn($entry) => $entry->value->toArray())
            ->toArray();

        $res2 = ArrayList::collect($foos)
            ->groupBy(fn(Foo $foo) => $foo->a)
            ->map(fn($entry) => $entry->value->toArray())
            ->toArray();

        $res3 = ArrayList::collect($foos)
            ->map(fn(Foo $foo) => $foo->a)
            ->groupBy(fn(int $a) => $a)
            ->map(fn($entry) => $entry->value->toArray())
            ->toArray();

        $this->assertEquals([[$f1, [$f1, $f3]], [$f2, [$f2]], [$f4, [$f4]]], $res1);
        $this->assertEquals([[1, [$f1, $f3]], [2, [$f2]], [3, [$f4]]], $res2);
        $this->assertEquals([[1, [1, 1]], [2, [2]], [3, [3]]], $res3);
    }
}
