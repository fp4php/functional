<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptyMap;

use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyMap;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Generator;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class NonEmptyMapOpsTest extends TestCase
{
    public function testGet(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]);

        $this->assertEquals(2, $hm->get('b')->get());
        $this->assertEquals(2, $hm('b')->get());
    }

    public function testUpdatedAndRemoved(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]);
        $hm = $hm->updated('c', 3);
        $hm = $hm->removed('a');

        $this->assertEquals([['b', 2], ['c', 3]], $hm->toList());
    }

    public function testEvery(): void
    {
        $this->assertTrue(NonEmptyHashMap::collectNonEmpty(['a' => 0, 'b' => 1])->every(fn($entry) => $entry >= 0));
        $this->assertFalse(NonEmptyHashMap::collectNonEmpty(['a' => 0, 'b' => 1])->every(fn($entry) => $entry > 0));
    }

    public function testEveryOf(): void
    {
        $this->assertFalse(
            NonEmptyHashMap::collectNonEmpty(['a' => new Foo(1), 'b' => new Bar(2)])->everyOf(Foo::class),
        );
        $this->assertTrue(
            NonEmptyHashMap::collectNonEmpty(['a' => new Foo(1), 'b' => new Foo(2)])->everyOf(Foo::class),
        );
    }

    public function testExists(): void
    {
        $this->assertTrue(NonEmptyHashMap::collectNonEmpty(['a' => 0, 'b' => 1])->exists(fn($entry) => $entry >= 0));
        $this->assertFalse(NonEmptyHashMap::collectNonEmpty(['a' => 0, 'b' => 1])->exists(fn($entry) => $entry > 1));
    }

    public function provideTestTraverseData(): Generator
    {
        yield NonEmptyHashMap::class => [
            NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'snd' => 2, 'thr' => 3]),
            NonEmptyHashMap::collectNonEmpty(['zro' => 0, 'fst' => 1, 'thr' => 2]),
        ];
    }

    /**
     * @param NonEmptyMap<string, int> $map1
     * @param NonEmptyMap<string, int> $map2
     *
     * @dataProvider provideTestTraverseData
     */
    public function testTraverseOption(NonEmptyMap $map1, NonEmptyMap $map2): void
    {
        $this->assertEquals(
            Option::some($map1),
            $map1->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
        $this->assertEquals(
            Option::none(),
            $map2->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none()),
        );
        $this->assertEquals(
            Option::some($map1),
            $map1->map(fn($x) => $x >= 1 ? Option::some($x) : Option::none())->sequenceOption(),
        );
        $this->assertEquals(
            Option::none(),
            $map2->map(fn($x) => $x >= 1 ? Option::some($x) : Option::none())->sequenceOption(),
        );
    }

    /**
     * @param NonEmptyMap<string, int> $map1
     * @param NonEmptyMap<string, int> $map2
     *
     * @dataProvider provideTestTraverseData
     */
    public function testTraverseEither(NonEmptyMap $map1, NonEmptyMap $map2): void
    {
        $this->assertEquals(
            Either::right($map1),
            $map1->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err')),
        );
        $this->assertEquals(
            Either::left('err'),
            $map2->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err')),
        );
        $this->assertEquals(
            Either::right($map1),
            $map1->map(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'))->sequenceEither(),
        );
        $this->assertEquals(
            Either::left('err'),
            $map2->map(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'))->sequenceEither(),
        );
    }

    public function testPartition(): void
    {
        $expected = Separated::create(
            HashMap::collect(['k3' => 3, 'k4' => 4, 'k5' => 5]),
            HashMap::collect(['k0' => 0, 'k1' => 1, 'k2' => 2]),
        );

        $actual = NonEmptyHashMap::collectNonEmpty(['k0' => 0, 'k1' => 1, 'k2' => 2, 'k3' => 3, 'k4' => 4, 'k5' => 5])
            ->partition(fn($i) => $i < 3);

        $this->assertEquals($expected, $actual);
    }

    public function testPartitionMap(): void
    {
        $expected = Separated::create(
            HashMap::collect(['k5' => 'L:5']),
            HashMap::collect(['k0' => 'R:0', 'k1' => 'R:1', 'k2' => 'R:2', 'k3' => 'R:3', 'k4' => 'R:4']),
        );

        $actual = NonEmptyHashMap::collectNonEmpty(['k0' => 0, 'k1' => 1, 'k2' => 2, 'k3' => 3, 'k4' => 4, 'k5' => 5])
            ->partitionMap(fn($i) => $i >= 5 ? Either::left("L:{$i}") : Either::right("R:{$i}"));

        $this->assertEquals($expected, $actual);
    }

    public function testFilter(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', new Foo(1)], ['b', 1], ['c',  new Foo(2)]]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($e) => $e === 1)->toList());
        $this->assertEquals([['b', 1]], $hm->filterKV(fn($key, $value) => $key === 'b' && $value === 1)->toList());
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [['b', 1], ['c', 2]],
            NonEmptyHashMap::collectPairsNonEmpty([['a', 'zero'], ['b', '1'], ['c', '2']])
                ->filterMap(fn($val) => is_numeric($val) ? Option::some((int) $val) : Option::none())
                ->toList()
        );
    }

    public function testFlatten(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                'fst' => 1,
                'snd' => 2,
                'thr' => 3,
            ]),
            NonEmptyHashMap::collectNonEmpty([
                NonEmptyHashMap::collectNonEmpty(['fst' => 1]),
                NonEmptyHashMap::collectNonEmpty(['snd' => 2]),
                NonEmptyHashMap::collectNonEmpty(['thr' => 3]),
            ])->flatten(),
        );
    }

    public function testFlatMap(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
            ]),
            NonEmptyHashMap::collectPairsNonEmpty([['2', 2], ['5', 5]])
                ->flatMap(fn(int $val) => [
                    ($val - 1) => $val - 1,
                    ($val) => $val,
                    ($val + 1) => $val + 1,
                ]),
        );
    }

    public function testFold(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['2', 2], ['3', 3]]);

        $this->assertEquals(6, $hm->fold(1)(fn($acc, $cur) => $acc + $cur));
    }

    public function testTap(): void
    {
        $this->assertEquals(
            [[2, 22], [3, 33]],
            NonEmptyHashMap::collectNonEmpty([2 => 22, 3 => 33])
                ->tap(fn(int $v) => $v + 10)
                ->toList(),
        );
    }

    public function testMap(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', 23], ['3', 34]],
            $hm->map(fn($e) => $e + 1)->toList()
        );

        $this->assertEquals(
            [['2', '2-22'], ['3', '3-33']],
            $hm->mapKV(fn($key, $elem) => "{$key}-{$elem}")->toList()
        );
    }

    public function testMapN(): void
    {
        $tuples = [
            'fst' => [1, true, true],
            'snd' => [2, true, false],
            'thr' => [3, false, false],
        ];

        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                'fst' => new Foo(1, true, true),
                'snd' => new Foo(2, true, false),
                'thr' => new Foo(3, false, false),
            ]),
            NonEmptyHashMap::collectNonEmpty($tuples)->mapN(Foo::create(...)),
        );
    }

    public function testReindex(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [[23, 22], [34, 33]],
            $hm->reindex(fn($v) => $v + 1)->toList(),
        );

        $this->assertEquals(
            [['2-22', 22], ['3-33', 33]],
            $hm->reindexKV(fn($k, $v) => "{$k}-{$v}")->toList(),
        );
    }

    public function testGroupBy(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                'odd' => NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'trd' => 3]),
                'even' => NonEmptyHashMap::collectNonEmpty(['snd' => 2]),
            ]),
            NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'snd' => 2, 'trd' => 3])
                ->groupBy(fn($i) => 0 === $i % 2 ? 'even' : 'odd'),
        );
    }

    public function testGroupMap(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                'odd' => NonEmptyHashMap::collectNonEmpty(['fst' => '1', 'trd' => '3']),
                'even' => NonEmptyHashMap::collectNonEmpty(['snd' => '2']),
            ]),
            NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'snd' => 2, 'trd' => 3])
                ->groupMap(fn($i) => 0 === $i % 2 ? 'even' : 'odd', fn($i) => (string) $i),
        );
    }

    public function testGroupMapReduce(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty([
                'oddDoubledSum' => 8,
                'evenDoubledSum' => 4,
            ]),
            NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'snd' => 2, 'trd' => 3])
                ->groupMapReduce(
                    fn(int $i) => 0 === $i % 2 ? 'evenDoubledSum' : 'oddDoubledSum',
                    fn(int $i) => $i * 2,
                    fn(int $old, int $new) => $old + $new,
                ),
        );
    }

    public function testKeys(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toList()
        );
    }

    public function testValues(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['a', 22], ['b', 33]]);

        $this->assertEquals(
            [22, 33],
            $hm->values()->toList()
        );
    }

    public function testForeach(): void
    {
        $expected = ['fst' => 1, 'snd' => 2, 'thr' => 3];
        $actual = [];

        foreach (NonEmptyHashMap::collectNonEmpty($expected) as $key => $value) {
            $actual[$key] = $value;
        }

        $this->assertEquals($expected, $actual);
    }

    public function testMerge(): void
    {
        $this->assertEquals(
            NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2, 'c' => 3]),
            NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 3])->merge(HashMap::collect(['b' => 2, 'c' => 3])),
        );
    }
}
