<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptyMap;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]])->getUnsafe()->toArray(),
        );

        $this->assertNull(
            NonEmptyHashMap::collectPairs([])->get(),
        );

        $this->assertNull(
            Option::try(fn() => NonEmptyHashMap::collectUnsafe([]))->get(),
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]])->get()?->toArray()
        );

        $this->assertNull(
            NonEmptyHashMap::collect([])->get()?->toArray()
        );

        $this->assertEquals(
            [['a', 1]],
            NonEmptyHashMap::collectNonEmpty(['a' => 1])->toArray()
        );

        $this->assertEquals(
            [['a', 1]],
            NonEmptyHashMap::collect(['a' => 1])->get()?->toArray()
        );

        $this->assertEquals(
            [['a', 1], ['b', 2]],
            NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]])->toArray(),
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
