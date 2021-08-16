<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashSet;

use Fp\Collections\NonEmptyHashSet;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

final class NonEmptyHashSetOpsTest extends TestCase
{
    public function testContains(): void
    {
        /** @var NonEmptyHashSet<int> $hs */
        $hs = NonEmptyHashSet::collectNonEmpty([1, 2, 2]);

        $this->assertTrue($hs->contains(1));
        $this->assertTrue($hs->contains(2));
        $this->assertFalse($hs->contains(3));

        $this->assertTrue($hs(1));
        $this->assertTrue($hs(2));
        $this->assertFalse($hs(3));
    }

    public function testUpdatedAndRemoved(): void
    {
        /** @var NonEmptyHashSet<int> $hs */
        $hs = NonEmptyHashSet::collectNonEmpty([1, 2, 2])->updated(3)->removed(1);

        $this->assertEquals([2, 3], $hs->toArray());
    }

    public function testEvery(): void
    {
        $hs = NonEmptyHashSet::collectNonEmpty([0, 1, 2, 3, 4, 5]);

        $this->assertTrue($hs->every(fn($i) => $i >= 0));
        $this->assertFalse($hs->every(fn($i) => $i > 0));
    }

    public function testExists(): void
    {
        /** @var NonEmptyHashSet<object|scalar> $hs */
        $hs = NonEmptyHashSet::collectNonEmpty([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hs->exists(fn($i) => $i === 1));
        $this->assertFalse($hs->exists(fn($i) => $i === 2));
    }

    public function testFilter(): void
    {
        $hs = NonEmptyHashSet::collectNonEmpty([new Foo(1), 1, 1, new Foo(1)]);
        $this->assertEquals([1], $hs->filter(fn($i) => $i === 1)->toArray());
    }

    public function testReduce(): void
    {
        /** @var NonEmptyHashSet<string> $list */
        $list = NonEmptyHashSet::collectNonEmpty(['1', '2', '3']);

        $this->assertEquals(
            '123',
            $list->reduce(fn($acc, $e) => $acc . $e)
        );
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            NonEmptyHashSet::collectNonEmpty([1, 2, 2, 3])->map(fn($e) => (string) ($e + 1))->toArray()
        );
    }
}
