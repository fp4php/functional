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
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;
use Generator;
use PHPUnit\Framework\TestCase;

use function Fp\Cast\asPairs;

final class SeqTest extends TestCase
{
    public function provideToStringData(): Generator
    {
        yield 'ArrayList<int>' => [
            ArrayList::collect([1, 2, 3]),
            'ArrayList(1, 2, 3)',
        ];
        yield 'ArrayList<Option<int>>' => [
            ArrayList::collect([
                Option::some(1),
                Option::some(2),
                Option::none(),
            ]),
            'ArrayList(Some(1), Some(2), None)',
        ];
        yield 'ArrayList<Either<string, int>>' => [
            ArrayList::collect([
                Either::right(1),
                Either::right(2),
                Either::left('err'),
            ]),
            'ArrayList(Right(1), Right(2), Left(\'err\'))',
        ];
        yield 'LinkedList<int>' => [
            LinkedList::collect([1, 2, 3]),
            'LinkedList(1, 2, 3)',
        ];
        yield 'LinkedList<Option<int>>' => [
            LinkedList::collect([
                Option::some(1),
                Option::some(2),
                Option::none(),
            ]),
            'LinkedList(Some(1), Some(2), None)',
        ];
        yield 'LinkedList<Either<string, int>>' => [
            LinkedList::collect([
                Either::right(1),
                Either::right(2),
                Either::left('err'),
            ]),
            'LinkedList(Right(1), Right(2), Left(\'err\'))',
        ];
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(Seq $seq, string $expected): void
    {
        $this->assertEquals($expected, (string)$seq);
    }

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

    public function provideTestCastsToArrayData(): Generator
    {
        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];
        yield ArrayList::class => [$expected, ArrayList::collect(asPairs($expected)), ArrayList::empty()];
        yield LinkedList::class => [$expected, LinkedList::collect(asPairs($expected)), LinkedList::empty()];
    }

    /**
     * @param array<string, int> $expected
     * @param Seq<array{string, int}> $seq
     * @param Seq<array{string, int}> $emptySeq
     * @dataProvider provideTestCastsToArrayData
     */
    public function testCastToArray(array $expected, Seq $seq, Seq $emptySeq): void
    {
        $this->assertEquals($expected, $seq->toArray());
        $this->assertEquals(Option::some($expected), $seq->toNonEmptyArray());
        $this->assertEquals(Option::none(), $emptySeq->toNonEmptyArray());
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

        $this->assertEquals(
            Stream::emits([1, 2, 3])->toList(),
            $seq->toStream()->toList(),
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
