<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\Seq;
use Fp\Functional\Option\Option;
use Generator;
use PHPUnit\Framework\TestCase;

final class SeqTest extends TestCase
{
    public function provideTestCastsData(): Generator
    {
        yield ArrayList::class => [ArrayList::collect([1, 2, 3]), ArrayList::collect([])];
        yield LinkedList::class => [LinkedList::collect([1, 2, 3]), LinkedList::collect([])];
    }

    public function provideTestCastsToHashMapData(): Generator
    {
        $pairs = [
            ['fst', 1],
            ['snd', 2],
            ['trd', 3],
        ];
        yield ArrayList::class => [$pairs, ArrayList::collect($pairs), ArrayList::empty()];
        yield LinkedList::class => [$pairs, LinkedList::collect($pairs), LinkedList::empty()];
    }

    /**
     * @param list<array{string, int}> $expected
     * @param Seq<array{string, int}> $seq
     * @param Seq<array{string, int}> $emptySeq
     * @dataProvider provideTestCastsToHashMapData
     */
    public function testCastsToHashMap(array $expected, Seq $seq, Seq $emptySeq): void
    {
        $this->assertEquals(
            HashMap::collectPairs($expected),
            $seq->toHashMap(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashMap::collectPairsUnsafe($expected)),
            $seq->toNonEmptyHashMap(),
        );

        $this->assertEquals(
            Option::none(),
            $emptySeq->toNonEmptyHashMap(),
        );
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(Seq $seq, Seq $emptySeq): void
    {
        $this->assertEquals(
            [1, 2, 3],
            $seq->toList(),
        );

        $this->assertEquals(
            Option::some([1, 2, 3]),
            $seq->toNonEmptyList(),
        );

        $this->assertEquals(
            Option::none(),
            $emptySeq->toNonEmptyList(),
        );

        $this->assertEquals(
            LinkedList::collect([1, 2, 3]),
            $seq->toLinkedList(),
        );

        $this->assertEquals(
            Option::some(NonEmptyLinkedList::collectNonEmpty([1, 2, 3])),
            $seq->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            Option::none(),
            $emptySeq->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            ArrayList::collect([1, 2, 3]),
            $seq->toArrayList(),
        );

        $this->assertEquals(
            Option::some(NonEmptyArrayList::collectNonEmpty([1, 2, 3])),
            $seq->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            Option::none(),
            $emptySeq->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            HashSet::collect([1, 2, 3]),
            $seq->toHashSet(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashSet::collectNonEmpty([1, 2, 3])),
            $seq->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            Option::none(),
            $emptySeq->toNonEmptyHashSet(),
        );
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(Seq $seq): void
    {
        $this->assertEquals(3, $seq->count());
        $this->assertEquals(3, $seq->count());
    }
}
