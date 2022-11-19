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
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals(
            "NonEmptyHashMap('k1' => 1, 'k2' => 2, 'k3' => 3)",
            (string) NonEmptyHashMap::collectPairsNonEmpty([
                ['k1', 1],
                ['k2', 2],
                ['k3', 3],
            ]),
        );
        $this->assertEquals(
            "NonEmptyHashMap('k1' => Some(1), 'k2' => Some(2), 'k3' => None)",
            (string) NonEmptyHashMap::collectPairsNonEmpty([
                ['k1', Option::some(1)],
                ['k2', Option::some(2)],
                ['k3', Option::none()],
            ]),
        );
        $this->assertEquals(
            "NonEmptyHashMap('k1' => 1, 'k2' => 2, 'k3' => 3)",
            NonEmptyHashMap::collectPairsNonEmpty([
                ['k1', 1],
                ['k2', 2],
                ['k3', 3],
            ])->toString(),
        );
        $this->assertEquals(
            "NonEmptyHashMap('k1' => Some(1), 'k2' => Some(2), 'k3' => None)",
            NonEmptyHashMap::collectPairsNonEmpty([
                ['k1', Option::some(1)],
                ['k2', Option::some(2)],
                ['k3', Option::none()],
            ])->toString(),
        );
    }

    public function testCasts(): void
    {
        $expected = [['a', 1], ['b', 2]];

        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toArray(),
        );

        $this->assertEquals(
            ['a' => 1, 'b' => 2],
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyArray(),
        );

        $this->assertEquals(
            $expected,
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toList(),
        );

        $this->assertEquals(
            $expected,
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyList(),
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

        $this->assertEquals(
            Stream::emits($expected)->toList(),
            NonEmptyHashMap::collectPairsNonEmpty($expected)->toStream()->toList(),
        );
    }

    public function testCount(): void
    {
        $this->assertCount(2, NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]]));
    }

    public function testToMergedArray(): void
    {
        $shapes = [
            'f' => ['fst' => 1],
            's' => ['snd' => 2],
            't' => ['thr' => 3],
        ];

        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals($expected, NonEmptyHashMap::collectNonEmpty($shapes)->toMergedArray());
    }

    public function testToNonEmptyMergedArray(): void
    {
        /** @var non-empty-array<string, non-empty-array<string, int>> $shapes */
        $shapes = [
            'f' => ['fst' => 1],
            's' => ['snd' => 2],
            't' => ['thr' => 3],
        ];

        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals($expected, NonEmptyHashMap::collectNonEmpty($shapes)->toNonEmptyMergedArray());
    }
}
