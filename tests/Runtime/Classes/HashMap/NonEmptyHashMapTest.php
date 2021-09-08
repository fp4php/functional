<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashMap;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptyHashMapTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            Option::try(fn() => NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]])->toArray())->get(),
        );

        $this->assertNull(
            Option::try(fn() => NonEmptyHashMap::collectPairs([])->toArray())->get(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectOption([['a', 1], ['b', 2]])->get()?->toArray()
        );

        $this->assertNull(
            NonEmptyHashMap::collectOption([])->get()?->toArray()
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            Option::try(fn() => NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]])->toArray())->get(),
        );

        $this->assertNull(
            Option::try(fn() => NonEmptyHashMap::collectPairsUnsafe([])->toArray())->get(),
        );

        $this->assertNull(
            Option::try(fn() => NonEmptyHashMap::collectPairsUnsafe([])->toArray())->get(),
        );

        $this->assertNull(
            Option::try(fn() => NonEmptyHashMap::collectPairsUnsafe([])->toArray())->get(),
        );
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toNonEmptyLinkedList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toNonEmptyArrayList()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toNonEmptyHashSet()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toHashMap()->toArray(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->toNonEmptyHashMap()->toArray(),
        );
    }

    public function testCount(): void
    {
        $this->assertEquals(
            2,
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->count(),
        );

        $this->assertCount(
            2,
            NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])
        );
    }
}
