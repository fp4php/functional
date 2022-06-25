<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySet;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptySet;
use Fp\Functional\Option\Option;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptySetTest extends TestCase
{
    public function provideTestCastsData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 3, 3]), [1, 2, 3]];
    }

    /**
     * @param NonEmptySet<int> $set
     * @param non-empty-list<int> $expected
     * @dataProvider provideTestCastsData
     */
    public function testCasts(NonEmptySet $set, array $expected): void
    {
        $this->assertEquals(
            $expected,
            $set->toArray(),
        );

        $this->assertEquals(
            $expected,
            $set->toNonEmptyArray(),
        );

        $this->assertEquals(
            LinkedList::collect($expected),
            $set->toLinkedList(),
        );

        $this->assertEquals(
            NonEmptyLinkedList::collectNonEmpty($expected),
            $set->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            ArrayList::collect($expected),
            $set->toArrayList(),
        );

        $this->assertEquals(
            NonEmptyArrayList::collectNonEmpty($expected),
            $set->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            HashSet::collect($expected),
            $set->toHashSet(),
        );

        $this->assertEquals(
            NonEmptyHashSet::collectNonEmpty($expected),
            $set->toNonEmptyHashSet(),
        );

    }

    public function testCastToHashMap(): void
    {
        $set = NonEmptyHashSet::collectNonEmpty([
            ['fst', 1],
            ['snd', 2],
            ['snd', 2],
            ['thd', 3],
            ['thd', 3],
        ]);

        $this->assertEquals(
            HashMap::collectPairs([
                ['fst', 1],
                ['snd', 2],
                ['thd', 3],
            ]),
            $set->toHashMap(),
        );
        $this->assertEquals(
            NonEmptyHashMap::collectPairsNonEmpty([
                ['fst', 1],
                ['snd', 2],
                ['thd', 3],
            ]),
            $set->toNonEmptyHashMap(),
        );
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySet $set): void
    {
        $this->assertEquals(3, $set->count());
    }
}
