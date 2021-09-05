<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashSet;

use Fp\Collections\HashSet;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class HashSetOpsTest extends TestCase
{
    public function testContains(): void
    {
        /** @psalm-var HashSet<int> $hs */
        $hs = HashSet::collect([1, 2, 2]);

        $this->assertTrue($hs->contains(1));
        $this->assertTrue($hs->contains(2));
        $this->assertFalse($hs->contains(3));

        $this->assertTrue($hs(1));
        $this->assertTrue($hs(2));
        $this->assertFalse($hs(3));
    }

    public function testUpdatedAndRemoved(): void
    {
        /** @psalm-var HashSet<int> $hs */
        $hs = HashSet::collect([1, 2, 2])->updated(3)->removed(1);

        $this->assertEquals([2, 3], $hs->toArray());
    }

    public function testEvery(): void
    {
        $hs = HashSet::collect([0, 1, 2, 3, 4, 5]);

        $this->assertTrue($hs->every(fn($i) => $i >= 0));
        $this->assertFalse($hs->every(fn($i) => $i > 0));
    }

    public function testEveryOf(): void
    {
        $this->assertTrue(HashSet::collect([new Foo(1), new Foo(2)])->everyOf(Foo::class));
        $this->assertFalse(HashSet::collect([new Foo(1), new Bar(2)])->everyOf(Foo::class));
    }

    public function testExists(): void
    {
        /** @psalm-var HashSet<object|scalar> $hs */
        $hs = HashSet::collect([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hs->exists(fn($i) => $i === 1));
        $this->assertFalse($hs->exists(fn($i) => $i === 2));
    }

    public function testExistsOf(): void
    {
        $hs = HashSet::collect([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hs->existsOf(Foo::class));
        $this->assertFalse($hs->existsOf(Bar::class));
    }

    public function testFilter(): void
    {
        $hs = HashSet::collect([new Foo(1), 1, 1, new Foo(1)]);
        $this->assertEquals([1], $hs->filter(fn($i) => $i === 1)->toArray());
        $this->assertEquals([1], HashSet::collect([1, null])->filterNotNull()->toArray());
    }

    public function testFirstsAndLasts(): void
    {
        $hs = HashSet::collect(['1', 2, '3']);
        $this->assertEquals('1', $hs->first(fn($i) => is_string($i))->get());
        $this->assertEquals('1', $hs->firstElement()->get());

        $hs = HashSet::collect(['1', 2, '3']);
        $this->assertEquals('3', $hs->last(fn($i) => is_string($i))->get());
        $this->assertEquals('3', $hs->lastElement()->get());

        $hs = HashSet::collect([$f1 = new Foo(1), 2, new Foo(2)]);
        $this->assertEquals($f1, $hs->firstOf(Foo::class)->get());
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            HashSet::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
        );
    }

    public function testFold(): void
    {
        /** @psalm-var HashSet<int> $list */
        $list = HashSet::collect([2, 3]);

        $this->assertEquals(
            6,
            $list->fold(1, fn(int $acc, $e) => $acc + $e)
        );
    }

    public function testReduce(): void
    {
        /** @psalm-var HashSet<string> $list */
        $list = HashSet::collect(['1', '2', '3']);

        $this->assertEquals(
            '123',
            $list->reduce(fn(string $acc, $e) => $acc . $e)->get()
        );
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            HashSet::collect([1, 2, 2, 3])->map(fn($e) => (string) ($e + 1))->toArray()
        );
    }
}
