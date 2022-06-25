<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\Seq;
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
        $this->assertEquals($expected, $seq->toHashMap()->toArray());
        $this->assertEquals($expected, $seq->toNonEmptyHashMap()->getUnsafe()->toArray());
        $this->assertNull($emptySeq->toNonEmptyHashMap()->get());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(Seq $seq, Seq $emptySeq): void
    {
        $this->assertEquals([1, 2, 3], $seq->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyArray()->getUnsafe());
        $this->assertNull($emptySeq->toNonEmptyArrayList()->get());
        $this->assertEquals([1, 2, 3], $seq->toLinkedList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toLinkedList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyLinkedList()->getUnsafe()->toArray());
        $this->assertNull($emptySeq->toNonEmptyLinkedList()->get());
        $this->assertEquals([1, 2, 3], $seq->toArrayList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyArrayList()->getUnsafe()->toArray());
        $this->assertNull($emptySeq->toNonEmptyArrayList()->get());
        $this->assertEquals([1, 2, 3], $seq->toArrayList()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toHashSet()->toArray());
        $this->assertEquals([1, 2, 3], $seq->toNonEmptyHashSet()->getUnsafe()->toArray());
        $this->assertNull($emptySeq->toNonEmptyHashSet()->get());
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
