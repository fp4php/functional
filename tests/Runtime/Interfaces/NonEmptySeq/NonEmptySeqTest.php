<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySeq;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptySeq;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Streams\Stream;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptySeqTest extends TestCase
{
    public function provideToStringData(): Generator
    {
        yield 'NonEmptyArrayList<int>' => [
            NonEmptyArrayList::collectNonEmpty([1, 2, 3]),
            'NonEmptyArrayList(1, 2, 3)',
        ];
        yield 'NonEmptyArrayList<Option<int>>' => [
            NonEmptyArrayList::collectNonEmpty([
                Option::some(1),
                Option::some(2),
                Option::none(),
            ]),
            'NonEmptyArrayList(Some(1), Some(2), None)',
        ];
        yield 'NonEmptyArrayList<Either<string, int>>' => [
            NonEmptyArrayList::collectNonEmpty([
                Either::right(1),
                Either::right(2),
                Either::left('err'),
            ]),
            'NonEmptyArrayList(Right(1), Right(2), Left(\'err\'))',
        ];
        yield 'NonEmptyLinkedList<int>' => [
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3]),
            'NonEmptyLinkedList(1, 2, 3)',
        ];
        yield 'NonEmptyLinkedList<Option<int>>' => [
            NonEmptyLinkedList::collectNonEmpty([
                Option::some(1),
                Option::some(2),
                Option::none(),
            ]),
            'NonEmptyLinkedList(Some(1), Some(2), None)',
        ];
        yield 'NonEmptyLinkedList<Either<string, int>>' => [
            NonEmptyLinkedList::collectNonEmpty([
                Either::right(1),
                Either::right(2),
                Either::left('err'),
            ]),
            'NonEmptyLinkedList(Right(1), Right(2), Left(\'err\'))',
        ];
    }

    /**
     * @dataProvider provideToStringData
     */
    public function testToString(NonEmptySeq $seq, string $expected): void
    {
        $this->assertEquals($expected, (string)$seq);
    }

    public function provideTestCastsData(): Generator
    {
        yield NonEmptyArrayList::class => [NonEmptyArrayList::collectNonEmpty([1, 2, 3])];
        yield NonEmptyLinkedList::class => [NonEmptyLinkedList::collectNonEmpty([1, 2, 3])];
    }

    public function provideTestCastsToHashMapData(): Generator
    {
        $pairs = [
            ['fst', 1],
            ['snd', 2],
            ['trd', 3],
        ];
        yield NonEmptyArrayList::class => [$pairs, NonEmptyArrayList::collectNonEmpty($pairs)];
        yield NonEmptyLinkedList::class => [$pairs, NonEmptyLinkedList::collectNonEmpty($pairs)];
    }

    /**
     * @param non-empty-list<array{string, int}> $expected
     * @param NonEmptySeq<array{string, int}> $seq
     * @dataProvider provideTestCastsToHashMapData
     */
    public function testCastsToHashMap(array $expected, NonEmptySeq $seq): void
    {
        $this->assertEquals(HashMap::collectPairs($expected), $seq->toHashMap());
        $this->assertEquals(NonEmptyHashMap::collectPairsNonEmpty($expected), $seq->toNonEmptyHashMap());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(NonEmptySeq $seq): void
    {
        $this->assertEquals(
            [1, 2, 3],
            $seq->toList(),
        );

        $this->assertEquals(
            [1, 2, 3],
            $seq->toNonEmptyList(),
        );

        $this->assertEquals(
            LinkedList::collect([1, 2, 3]),
            $seq->toLinkedList(),
        );

        $this->assertEquals(
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3]),
            $seq->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            ArrayList::collect([1, 2, 3]),
            $seq->toArrayList(),
        );

        $this->assertEquals(
            NonEmptyArrayList::collectNonEmpty([1, 2, 3]),
            $seq->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            HashSet::collect([1, 2, 3]),
            $seq->toHashSet(),
        );

        $this->assertEquals(
            NonEmptyHashSet::collectNonEmpty([1, 2, 3]),
            $seq->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            Stream::emits([1, 2, 3])->toList(),
            $seq->toStream()->toList(),
        );
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySeq $seq): void
    {
        $this->assertEquals(3, $seq->count());
        $this->assertEquals(3, $seq->count());
    }
}
