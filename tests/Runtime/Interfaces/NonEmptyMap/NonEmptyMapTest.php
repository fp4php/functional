<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptyMap;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapTest extends TestCase
{
    public function testCasts(): void
    {
        $expected = [['a', 1], ['b', 2]];

        $this->assertEquals(
            $expected,
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toArray(),
        );

        $this->assertEquals(
            $expected,
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyArray(),
        );

        $this->assertEquals(
            LinkedList::collect($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toLinkedList(),
        );

        $this->assertEquals(
            NonEmptyLinkedList::collectNonEmpty($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            ArrayList::collect($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toArrayList(),
        );

        $this->assertEquals(
            NonEmptyArrayList::collectNonEmpty($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            HashSet::collect($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toHashSet(),
        );

        $this->assertEquals(
            NonEmptyHashSet::collectNonEmpty($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            HashMap::collectPairs($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toHashMap(),
        );

        $this->assertEquals(
            NonEmptyHashMap::collectPairsNonEmpty($expected),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyHashMap(),
        );

    }

    public function testCount(): void
    {
        $this->assertCount(2, NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]]));
    }
}
