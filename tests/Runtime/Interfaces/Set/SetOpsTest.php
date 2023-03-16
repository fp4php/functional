<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Set;

use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\Set;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Evidence\of;

final class SetOpsTest extends TestCase
{
    public function testContains(): void
    {
        /** @psalm-var HashSet<int> $hs */
        $hs = HashSet::collect([1, 2, 2]);

        $this->assertTrue($hs->contains(1));
        $this->assertTrue($hs->contains(2));
        $this->assertFalse($hs->contains(3));

        $this->assertTrue($hs(1));
        $this->assertTrue($hs(2));
        $this->assertFalse($hs(3));
    }

    public function testUpdatedAndRemoved(): void
    {
        /** @psalm-var HashSet<int> $hs */
        $hs = HashSet::collect([1, 2, 2])->appended(3)->removed(1);

        $this->assertEquals([2, 3], $hs->toList());
    }

    public function testAppendedAll(): void
    {
        $hs = HashSet::collect([1, 2, 2])->appendedAll([3, 3, 4]);
        $this->assertEquals(HashSet::collect([1, 2, 3, 4]), $hs);
    }

    public function testEvery(): void
    {
        $hs = HashSet::collect([0, 1, 2, 3, 4, 5]);

        $this->assertTrue($hs->every(fn($i) => $i >= 0));
        $this->assertFalse($hs->every(fn($i) => $i > 0));
    }

    public function testEveryN(): void
    {
        $this->assertTrue(HashSet::collect([[1, 1], [2, 2], [3, 3]])->everyN(fn(int $a, int $b) => ($a + $b) <= 6));
        $this->assertFalse(HashSet::collect([[1, 1], [2, 2], [3, 3]])->everyN(fn(int $a, int $b) => ($a + $b) < 6));
    }

    public function provideTestTraverseData(): Generator
    {
        yield HashSet::class => [
            HashSet::collect([1, 2, 3]),
            HashSet::collect([0, 1, 2]),
        ];
    }

    /**
     * @param Set<int> $set1
     * @param Set<int> $set2
     *
     * @dataProvider provideTestTraverseData
     */
    public function testTraverseOption(Set $set1, Set $set2): void
    {
        $this->assertEquals(
            Option::some($set1),
            $set1->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
        $this->assertEquals(
            Option::none(),
            $set2->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
        $this->assertEquals(
            Option::some($set1),
            $set1->map(fn($x) => $x >= 1 ? Option::some($x) : Option::none())->sequenceOption(),
        );
        $this->assertEquals(
            Option::none(),
            $set2->map(fn($x) => $x >= 1 ? Option::some($x) : Option::none())->sequenceOption(),
        );
    }

    public function testTraverseOptionN(): void
    {
        $collection = HashSet::collect([
            [1, 1],
            [2, 2],
            [3, 3],
        ]);

        $this->assertEquals(
            Option::some(HashSet::collect([2, 4, 6])),
            $collection->traverseOptionN(
                fn(int $a, int $b) => $a + $b <= 6 ? Option::some($a + $b) : Option::none(),
            ),
        );
        $this->assertEquals(
            Option::none(),
            $collection->traverseOptionN(
                fn(int $a, int $b) => $a + $b < 6 ? Option::some($a + $b) : Option::none(),
            ),
        );
    }

    /**
     * @param Set<int> $set1
     * @param Set<int> $set2
     *
     * @dataProvider provideTestTraverseData
     */
    public function testTraverseEither(Set $set1, Set $set2): void
    {
        $this->assertEquals(
            Either::right($set1),
            $set1->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err')),
        );
        $this->assertEquals(
            Either::left('err'),
            $set2->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err')),
        );
        $this->assertEquals(
            Either::right($set1),
            $set1->map(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'))->sequenceEither(),
        );
        $this->assertEquals(
            Either::left('err'),
            $set2->map(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'))->sequenceEither(),
        );
    }

    public function testTraverseEitherN(): void
    {
        $collection = HashSet::collect([
            [1, 1],
            [2, 2],
            [3, 3],
        ]);

        $this->assertEquals(
            Either::right(HashSet::collect([2, 4, 6])),
            $collection->traverseEitherN(
                fn(int $a, int $b) => $a + $b <= 6 ? Either::right($a + $b) : Either::left('invalid'),
            ),
        );
        $this->assertEquals(
            Either::left('invalid'),
            $collection->traverseEitherN(
                fn(int $a, int $b) => $a + $b < 6 ? Either::right($a + $b) : Either::left('invalid'),
            ),
        );
    }

    public function testTraverseEitherMerged(): void
    {
        /** @var HashSet<int> $seq1 */
        $seq1 = HashSet::collect([1, 2, 3]);

        $this->assertEquals(
            Either::right($seq1),
            $seq1->traverseEitherMerged(fn($x) => $x >= 1 ? Either::right($x) : Either::left(['err'])),
        );
        $this->assertEquals(
            Either::right($seq1),
            $seq1->map(fn($x) => $x >= 1 ? Either::right($x) : Either::left(['err']))->sequenceEitherMerged(),
        );

        /** @var HashSet<int> $seq2 */
        $seq2 = HashSet::collect([-2, -1, 0, 1, 2]);

        $this->assertEquals(
            Either::left(['wrong: -2', 'wrong: -1', 'wrong: 0']),
            $seq2->traverseEitherMerged(fn($x) => $x >= 1 ? Either::right($x) : Either::left(["wrong: {$x}"])),
        );
        $this->assertEquals(
            Either::left(['wrong: -2', 'wrong: -1', 'wrong: 0']),
            $seq2->map(fn($x) => $x >= 1 ? Either::right($x) : Either::left(["wrong: {$x}"]))->sequenceEitherMerged(),
        );
    }

    public function testTraverseEitherMergedN(): void
    {
        $collection = HashSet::collect([
            [1, 1],
            [2, 2],
            [3, 3],
            [4, 4],
        ]);

        $this->assertEquals(
            Either::right(HashSet::collect([2, 4, 6, 8])),
            $collection->traverseEitherMergedN(
                fn(int $a, int $b) => $a + $b <= 8 ? Either::right($a + $b) : Either::left(['invalid']),
            ),
        );
        $this->assertEquals(
            Either::left(['invalid: 3 + 3', 'invalid: 4 + 4']),
            $collection->traverseEitherMergedN(
                fn(int $a, int $b) => $a + $b < 6 ? Either::right($a + $b) : Either::left(["invalid: {$a} + {$b}"]),
            ),
        );
    }

    public function testPartition(): void
    {
        $this->assertEquals(
            Separated::create(
                HashSet::collect([3, 4, 5]),
                HashSet::collect([0, 1, 2]),
            ),
            HashSet::collect([0, 1, 2, 3, 4, 5])->partition(fn($i) => $i < 3),
        );
    }

    public function testPartitionN(): void
    {
        $collection = HashSet::collect([
            [1, 1, 'lhs'],
            [1, 1, 'lhs'],
            [1, 2, 'lhs'],
            [1, 2, 'lhs'],
            [2, 2, 'rhs'],
            [2, 2, 'rhs'],
            [3, 3, 'rhs'],
            [3, 3, 'rhs'],
        ]);

        $expected = Separated::create(
            left: HashSet::collect([
                [1, 1, 'lhs'],
                [1, 1, 'lhs'],
                [1, 2, 'lhs'],
                [1, 2, 'lhs'],
            ]),
            right: HashSet::collect([
                [2, 2, 'rhs'],
                [2, 2, 'rhs'],
                [3, 3, 'rhs'],
                [3, 3, 'rhs'],
            ]),
        );
        $actual = $collection->partitionN(fn(int $a, int $b) => ($a + $b) >= 4);

        $this->assertEquals($expected, $actual);
    }

    public function testPartitionMap(): void
    {
        $this->assertEquals(
            Separated::create(
                HashSet::collect(['L: 5']),
                HashSet::collect(['R: 0', 'R: 1', 'R: 2', 'R: 3', 'R: 4']),
            ),
            HashSet::collect([0, 1, 2, 3, 4, 5])
                ->partitionMap(fn($i) => $i >= 5 ? Either::left("L: {$i}") : Either::right("R: {$i}")),
        );
    }

    public function testPartitionMapN(): void
    {
        $collection = HashSet::collect([
            [1, 1, 'lhs'],
            [1, 1, 'lhs'],
            [1, 2, 'lhs'],
            [1, 2, 'lhs'],
            [2, 2, 'rhs'],
            [2, 2, 'rhs'],
            [3, 3, 'rhs'],
            [3, 3, 'rhs'],
        ]);

        $expected = Separated::create(
            left: HashSet::collect([
                [1, 1, 'lhs'],
                [1, 1, 'lhs'],
                [1, 2, 'lhs'],
                [1, 2, 'lhs'],
            ]),
            right: HashSet::collect([
                [2, 2, 'rhs'],
                [2, 2, 'rhs'],
                [3, 3, 'rhs'],
                [3, 3, 'rhs'],
            ]),
        );
        $actual = $collection->partitionMapN(fn(int $a, int $b, string $mark) => Either::when(
            cond: ($a + $b) >= 4,
            right: fn() => [$a, $b, $mark],
            left: fn() => [$a, $b, $mark],
        ));

        $this->assertEquals($expected, $actual);
    }

    public function testExists(): void
    {
        /** @psalm-var HashSet<object|scalar> $hs */
        $hs = HashSet::collect([new Foo(1), 1, 1, new Foo(1)]);

        $this->assertTrue($hs->exists(fn($i) => $i === 1));
        $this->assertFalse($hs->exists(fn($i) => $i === 2));
    }

    public function testExistsN(): void
    {
        $this->assertTrue(HashSet::collect([[1, 1], [2, 2], [3, 3]])->existsN(fn(int $a, int $b) => ($a + $b) === 6));
        $this->assertFalse(HashSet::collect([[1, 1], [2, 2], [3, 3]])->existsN(fn(int $a, int $b) => ($a + $b) === 7));
    }

    public function testGroupBy(): void
    {
        $this->assertEquals(
            HashMap::collect([
                'odd' => NonEmptyHashSet::collectNonEmpty([3, 1]),
                'even' => NonEmptyHashSet::collectNonEmpty([2]),
            ]),
            HashSet::collect([1, 1, 2, 2, 3, 3])->groupBy(fn($i) => 0 === $i % 2 ? 'even' : 'odd'),
        );
    }

    public function testGroupMap(): void
    {
        $this->assertEquals(
            HashMap::collect([
                'odd' => NonEmptyHashSet::collectNonEmpty(['num-3', 'num-1']),
                'even' => NonEmptyHashSet::collectNonEmpty(['num-2']),
            ]),
            HashSet::collect([1, 1, 2, 2, 3, 3])->groupMap(
                fn($i) => 0 === $i % 2 ? 'even' : 'odd',
                fn($i) => "num-{$i}",
            ),
        );
    }

    public function testGroupMapReduce(): void
    {
        $this->assertEquals(
            HashMap::collect([
                10 => [10, 15, 20],
                20 => [10, 15],
                30 => [20],
            ]),
            HashSet::collect([
                ['id' => 10, 'sum' => 10],
                ['id' => 10, 'sum' => 15],
                ['id' => 10, 'sum' => 20],
                ['id' => 20, 'sum' => 10],
                ['id' => 20, 'sum' => 15],
                ['id' => 30, 'sum' => 20],
            ])->groupMapReduce(
                fn(array $a) => $a['id'],
                fn(array $a) => [$a['sum']],
                fn(array $old, array $new) => array_merge($old, $new),
            )
        );
    }

    public function testFilter(): void
    {
        $this->assertEquals([1], HashSet::collect([new Foo(1), 1, 1, new Foo(1)])->filter(fn($i) => $i === 1)->toList());
        $this->assertEquals([1], HashSet::collect([1, null])->filterNotNull()->toList());
    }

    public function testFilterN(): void
    {
        $actual = HashSet::collect([[1, 1], [2, 2], [3, 3]])
            ->filterN(fn(int $a, int $b) => $a + $b >= 6);

        $this->assertEquals(HashSet::collect([[3, 3]]), $actual);
    }

    public function testFilterOf(): void
    {
        $hs = HashSet::collect([new Foo(1), 1, 2, new Foo(1)]);
        $this->assertCount(1, $hs->filterMap(of(Foo::class)));
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [1, 2],
            HashSet::collect(['zero', '1', '2'])
                ->filterMap(fn($e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
                ->toList()
        );
    }

    public function testFilterMapN(): void
    {
        $actual = HashSet::collect([[1, 1], [2, 2], [3, 3]])
            ->filterMapN(fn(int $a, int $b) => Option::when($a + $b >= 6, fn() => $a));

        $this->assertEquals(HashSet::collect([3]), $actual);
    }

    public function testFirst(): void
    {
        $this->assertEquals(Option::some('1'), HashSet::collect(['1', 2, '3'])->first(is_string(...)));
        $this->assertEquals(Option::none(), HashSet::collect([])->first(is_string(...)));
    }

    public function testFirstElement(): void
    {
        $this->assertEquals(Option::some('1'), HashSet::collect(['1', 2, '3'])->firstElement());
        $this->assertEquals(Option::none(), HashSet::collect([])->firstElement());
    }

    public function testFirstN(): void
    {
        $this->assertEquals(
            Option::some([2, 2, 'm3']),
            HashSet::collect([[1, 1, 'm1'], [1, 1, 'm2'], [2, 2, 'm3'], [2, 2, 'm4']])
                ->firstN(fn(int $a, int $b) => ($a + $b) === 4),
        );
    }

    public function testLast(): void
    {
        $this->assertEquals(Option::some('3'), HashSet::collect(['1', 2, '3'])->last(is_string(...)));
        $this->assertEquals(Option::none(), HashSet::collect([])->last(is_string(...)));
    }

    public function testLastElement(): void
    {
        $this->assertEquals(Option::some('3'), HashSet::collect(['1', 2, '3'])->lastElement());
        $this->assertEquals(Option::none(), HashSet::collect([])->lastElement());
    }

    public function testLastN(): void
    {
        $this->assertEquals(
            Option::some([2, 2, 'm4']),
            HashSet::collect([[1, 1, 'm1'], [1, 1, 'm2'], [2, 2, 'm3'], [2, 2, 'm4']])
                ->lastN(fn(int $a, int $b) => ($a + $b) === 4),
        );
    }

    public function testFlatten(): void
    {
        $this->assertEquals(
            HashSet::collect([]),
            HashSet::collect([])->flatten(),
        );
        $this->assertEquals(
            HashSet::collect([1, 2, 3, 4]),
            HashSet::collect([
                HashSet::collect([1, 1, 2]),
                HashSet::collect([2, 2, 3]),
                HashSet::collect([3, 3, 4]),
            ])->flatten(),
        );
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            HashSet::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList()
        );
    }

    public function testFlatMapN(): void
    {
        $this->assertEquals(
            HashSet::collect([2, 3, 4, 5, 6, 7]),
            HashSet::collect([[1, 2], [3, 4], [5, 6]])
                ->flatMapN(fn(int $a, int $b) => [$a + 1, $b + 1]),
        );
    }

    public function testFold(): void
    {
        $this->assertEquals(6, HashSet::collect([2, 3])->fold(1)(fn($acc, $e) => $acc + $e));
    }

    public function testMap(): void
    {
        $this->assertEquals(
            ['2', '3', '4'],
            HashSet::collect([1, 2, 2, 3])->map(fn($e) => (string) ($e + 1))->toList()
        );
    }

    public function testMapN(): void
    {
        $tuples = [
            [1, true, true],
            [2, true, false],
            [3, false, false],
        ];

        $this->assertEquals(
            HashSet::collect([
                new Foo(1, true, true),
                new Foo(2, true, false),
                new Foo(3, false, false),
            ]),
            HashSet::collect($tuples)->mapN(Foo::create(...)),
        );
    }

    public function testTap(): void
    {
        $this->assertEquals(
            [2, 3],
            HashSet::collect([new Foo(1), new Foo(2)])
                ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
                ->map(fn(Foo $foo) => $foo->a)
                ->toList()
        );
    }

    public function testTapN(): void
    {
        $this->assertEquals(
            HashSet::collect([2, 3]),
            HashSet::collect([[new Foo(1), 2], [new Foo(2), 3]])
                ->tapN(fn(Foo $foo, int $new) => $foo->a = $new)
                ->mapN(fn(Foo $foo) => $foo->a),
        );
    }

    public function testSubsetOf(): void
    {
        $this->assertTrue(HashSet::collect([])->subsetOf(HashSet::collect([])));
        $this->assertTrue(HashSet::collect([])->subsetOf(HashSet::collect([1, 2, 3])));
        $this->assertTrue(HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2])));
        $this->assertTrue(HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2, 3])));
        $this->assertFalse(HashSet::collect([1, 2, 3])->subsetOf(HashSet::collect([1, 2])));
    }

    public function testHead(): void
    {
        $this->assertEquals(
            1,
            HashSet::collect([1, 2, 3])->head()->get()
        );
    }

    public function testTail(): void
    {
        $this->assertEquals(
            [2, 3],
            HashSet::collect([1, 2, 3])->tail()->toList()
        );
    }

    public function testInit(): void
    {
        $this->assertEquals(
            [1, 2],
            HashSet::collect([1, 2, 3])->init()->toList()
        );
    }

    public function testIntersectAndDiff(): void
    {
        $this->assertEquals(
            [2, 3],
            HashSet::collect([1, 2, 3])
                ->intersect(HashSet::collect([2, 3]))
                ->toList()
        );

        $this->assertEquals(
            [1],
            HashSet::collect([1, 2, 3])
                ->diff(HashSet::collect([2, 3]))
                ->toList()
        );
    }

    public function testReindex(): void
    {
        $this->assertEquals(
            HashMap::collectPairs([
                ['key-1', 1],
                ['key-2', 2],
            ]),
            HashSet::collect([1, 2, 2])
                ->reindex(fn($value) => "key-{$value}"),
        );
    }

    public function testReindexN(): void
    {
        $this->assertEquals(
            HashMap::collectPairs([
                ['x-1', ['x', 1]],
                ['y-2', ['y', 2]],
                ['z-3', ['z', 3]],
            ]),
            HashSet::collect([['x', 1], ['y', 2], ['z', 3]])
                ->reindexN(fn(string $a, int $b) => "{$a}-{$b}"),
        );
    }

    public function testMkString(): void
    {
        $this->assertEquals('(1, 2, 3)', HashSet::collect([1, 2, 3])->mkString('(', ', ', ')'));
        $this->assertEquals('()', HashSet::collect([])->mkString('(', ', ', ')'));
    }

    public function testMax(): void
    {
        $this->assertEquals(Option::some(3), HashSet::collect([2, 1, 3])->max());
        $this->assertEquals(Option::none(), HashSet::collect([])->max());
    }

    public function testMaxBy(): void
    {
        $neSet = HashSet::collect([new Foo(2), new Foo(1), new Foo(3)]);

        /** @var HashSet<Foo> $emptySet */
        $emptySet = HashSet::collect([]);

        $this->assertEquals(
            Option::some(new Foo(3)),
            $neSet->maxBy(fn(Foo $obj) => $obj->a),
        );

        $this->assertEquals(
            Option::none(),
            $emptySet->maxBy(fn(Foo $obj) => $obj->a),
        );
    }

    public function testMin(): void
    {
        $this->assertEquals(Option::some(1), HashSet::collect([2, 1, 3])->min());
        $this->assertEquals(Option::none(), HashSet::collect([])->min());
    }

    public function testMinBy(): void
    {
        $neSet = HashSet::collect([new Foo(2), new Foo(1), new Foo(3)]);

        /** @var HashSet<Foo> $emptySet */
        $emptySet = HashSet::collect([]);

        $this->assertEquals(
            Option::some(new Foo(1)),
            $neSet->minBy(fn(Foo $obj) => $obj->a),
        );

        $this->assertEquals(
            Option::none(),
            $emptySet->minBy(fn(Foo $obj) => $obj->a),
        );
    }

    public function testFirstMap(): void
    {
        $this->assertEquals(
            Option::none(),
            HashSet::collect(['fst', 'snd', 'thr'])->firstMap(fn($i) => Option::when(is_numeric($i), fn() => (int) $i)),
        );

        $this->assertEquals(
            Option::some(1),
            HashSet::collect(['zero', '1', '2'])->firstMap(fn($i) => Option::when(is_numeric($i), fn() => (int) $i)),
        );
    }

    public function testLastMap(): void
    {
        $this->assertEquals(
            Option::none(),
            HashSet::collect(['fst', 'snd', 'thr'])->lastMap(fn($i) => Option::when(is_numeric($i), fn() => (int) $i)),
        );

        $this->assertEquals(
            Option::some(2),
            HashSet::collect(['zero', '1', '2'])->lastMap(fn($i) => Option::when(is_numeric($i), fn() => (int) $i)),
        );
    }
}
