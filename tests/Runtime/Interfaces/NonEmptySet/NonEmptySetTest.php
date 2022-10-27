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
use Fp\Streams\Stream;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

final class NonEmptySetTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals(
            'NonEmptyHashSet(1, 2, 3)',
            (string) NonEmptyHashSet::collectNonEmpty([1, 2, 3]),
        );
        $this->assertEquals(
            'NonEmptyHashSet(Some(1), Some(2), None)',
            (string) NonEmptyHashSet::collectNonEmpty([
                Option::some(1),
                Option::some(2),
                Option::none(),
            ]),
        );
        $this->assertEquals(
            'NonEmptyHashSet(1, 2, 3)',
            NonEmptyHashSet::collectNonEmpty([1, 2, 3])->toString(),
        );
        $this->assertEquals(
            'NonEmptyHashSet(Some(1), Some(2), None)',
            NonEmptyHashSet::collectNonEmpty([
                Option::some(1),
                Option::some(2),
                Option::none(),
            ])->toString(),
        );
    }

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
            $set->toList(),
        );

        $this->assertEquals(
            $expected,
            $set->toNonEmptyList(),
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

        $this->assertEquals(
            Stream::emits($expected)->toList(),
            $set->toStream()->toList(),
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

        $this->assertEquals(
            ['fst' => 1, 'snd' => 2, 'thr' => 3],
            NonEmptyHashSet::collectNonEmpty([['fst', 1], ['snd', 2], ['thr', 3]])->toArray(),
        );

        $this->assertEquals(
            ['fst' => 1, 'snd' => 2, 'thr' => 3],
            NonEmptyHashSet::collectNonEmpty([['fst', 1], ['snd', 2], ['thr', 3]])->toNonEmptyArray(),
        );
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySet $set): void
    {
        $this->assertEquals(3, $set->count());
    }

    public function testMax(): void
    {
        $this->assertEquals(3, NonEmptyHashSet::collectNonEmpty([2, 1, 3])->max());
    }

    public function testMaxBy(): void
    {
        $neSet = NonEmptyHashSet::collectNonEmpty([new Foo(2), new Foo(1), new Foo(3)]);

        $this->assertEquals(
            new Foo(3),
            $neSet->maxBy(fn(Foo $obj) => $obj->a),
        );
    }

    public function testMin(): void
    {
        $this->assertEquals(1, NonEmptyHashSet::collectNonEmpty([2, 1, 3])->min());
    }

    public function testMinBy(): void
    {
        $neSet = NonEmptyHashSet::collectNonEmpty([new Foo(2), new Foo(1), new Foo(3)]);

        $this->assertEquals(
            new Foo(1),
            $neSet->minBy(fn(Foo $obj) => $obj->a),
        );
    }

    public function testToMergedArray(): void
    {
        $shapes = [
            ['fst' => 1],
            ['snd' => 2],
            ['thr' => 3],
        ];

        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals($expected, NonEmptyHashSet::collectNonEmpty($shapes)->toMergedArray());
    }

    public function testToNonEmptyMergedArray(): void
    {
        $shapes = [
            ['fst' => 1],
            ['snd' => 2],
            ['thr' => 3],
        ];

        $expected = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals($expected, NonEmptyHashSet::collectNonEmpty($shapes)->toNonEmptyMergedArray());
    }
}
