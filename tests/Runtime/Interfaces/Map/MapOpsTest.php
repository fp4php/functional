<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Map;

use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

final class MapOpsTest extends TestCase
{
    public function testGet(): void
    {
        $hm = HashMap::collect(['a' => 1, 'b' => 2]);

        $this->assertEquals(2, $hm->get('b')->get());
        $this->assertEquals(2, $hm('b')->get());
    }

    public function testUpdatedAndRemoved(): void
    {
        $hm = HashMap::collect(['a' => 1, 'b' => 2]);
        $hm = $hm->updated('c', 3);
        $hm = $hm->removed('a');

        $this->assertEquals([['b', 2], ['c', 3]], $hm->toList());
    }

    public function testEvery(): void
    {
        $hm = HashMap::collect(['a' => 0, 'b' => 1]);

        $this->assertTrue($hm->every(fn($entry) => $entry >= 0));
        $this->assertFalse($hm->every(fn($entry) => $entry > 0));
    }

    public function testEveryOf(): void
    {
        $this->assertFalse(
            HashMap::collect(['a' => new Foo(1), 'b' => new Bar(2)])->everyOf(Foo::class),
        );
        $this->assertTrue(
            HashMap::collect(['a' => new Foo(1), 'b' => new Foo(2)])->everyOf(Foo::class),
        );
    }

    public function testExists(): void
    {
        $hm = HashMap::collect(['a' => 0, 'b' => 1]);

        $this->assertTrue($hm->exists(fn($entry) => $entry > 0));
        $this->assertFalse($hm->exists(fn($entry) => $entry > 1));
    }

    public function testEveryMap(): void
    {
        $hm = HashMap::collect([
            'a' => new Foo(1),
            'b' => new Foo(2),
        ]);

        $this->assertEquals(
            Option::some($hm),
            $hm->traverseOption(fn($x) => $x->a >= 1 ? Option::some($x) : Option::none())
        );
        $this->assertEquals(
            Option::none(),
            $hm->traverseOption(fn($x) => $x->a >= 2 ? Option::some($x) : Option::none())
        );
        $this->assertEquals(
            Option::some($hm),
            $hm->map(fn($x) => $x->a >= 1 ? Option::some($x) : Option::none())->sequenceOption()
        );
        $this->assertEquals(
            Option::none(),
            $hm->map(fn($x) => $x->a >= 2 ? Option::some($x) : Option::none())->sequenceOption()
        );
    }

    public function testFilter(): void
    {
        $hm = HashMap::collect(['a' => new Foo(1), 'b' => 1, 'c' => new Foo(2)]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($e) => $e === 1)->toList());
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [['b', 1], ['c', 2]],
            HashMap::collectPairs([['a', 'zero'], ['b', '1'], ['c', '2']])
                ->filterMap(fn($val) => is_numeric($val) ? Option::some((int) $val) : Option::none())
                ->toList()
        );
    }

    public function testFlatMap(): void
    {
        $hm = HashMap::collectPairs([['2', 2], ['5', 5]]);

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]],
            $hm->flatMap(fn($val) => [
                [$val - 1, $val - 1],
                [$val, $val],
                [$val + 1, $val + 1]
            ])->toList()
        );

        $this->assertEquals(
            [['2', 20], ['5', 5]],
            $hm->flatMap(fn($val) => [['2', 20], [(string) $val, $val]])->toList()
        );
    }

    public function testFold(): void
    {
        $hm = HashMap::collectPairs([['2', 2], ['3', 3]]);

        $this->assertEquals(6, $hm->fold(1)(fn($acc, $cur) => $acc + $cur));
    }

    public function testMap(): void
    {
        $hm = HashMap::collectPairs([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', 'val-22'], ['3', 'val-33']],
            $hm->map(fn($e) => "val-{$e}")->toList()
        );

        $this->assertEquals(
            [['2', 'key-2-val-22'], ['3', 'key-3-val-33']],
            $hm->mapKV(fn($key, $elem) => "key-{$key}-val-{$elem}")->toList()
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
            HashMap::collect([
                'fst' => new Foo(1, true, true),
                'snd' => new Foo(2, true, false),
                'thr' => new Foo(3, false, false),
            ]),
            HashMap::collect($tuples)->mapN(Foo::create(...)),
        );
    }

    public function testTap(): void
    {
        $hm = HashMap::collectPairs([['2', 22], ['3', 33]])
            ->tap(fn(int $v) => $v + 10);

        $this->assertEquals([['2', 22], ['3', 33]], $hm->toList());
    }

    public function testReindex(): void
    {
        $hm = HashMap::collectPairs([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [[23, 22], [34, 33]],
            $hm->reindex(fn($v) => $v + 1)->toList()
        );

        $this->assertEquals(
            [['2-22', 22], ['3-33', 33]],
            $hm->reindexKV(fn($k, $v) => "{$k}-{$v}")->toList()
        );
    }

    public function testGroupBy(): void
    {
        $this->assertEquals(
            HashMap::collect([
                'odd' => NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'trd' => 3]),
                'even' => NonEmptyHashMap::collectNonEmpty(['snd' => 2]),
            ]),
            HashMap::collect(['fst' => 1, 'snd' => 2, 'trd' => 3])->groupBy(fn($i) => 0 === $i % 2 ? 'even' : 'odd'),
        );
    }

    /**
     * ```php
     * >>> HashMap::collect([
     * >>>     '10-1' => ['id' => 10, 'sum' => 10],
     * >>>     '10-2' => ['id' => 10, 'sum' => 15],
     * >>>     '10-3' => ['id' => 10, 'sum' => 20],
     * >>>     '20-1' => ['id' => 20, 'sum' => 10],
     * >>>     '20-2' => ['id' => 20, 'sum' => 15],
     * >>>     '30-1' => ['id' => 30, 'sum' => 20],
     * >>> ])->groupMap(
     * >>>     fn(array $a) => $a['id'],
     * >>>     fn(array $a) => $a['sum'] + 1,
     * >>> );
     * => HashMap(
     * =>   10 -> NonEmptyHashMap('10-3' => 21, '10-2' => 16, '10-1' => 11),
     * =>   20 -> NonEmptyHashMap('20-2' => 16, '20-1' => 11),
     * =>   30 -> NonEmptyHashMap('30-1' => 21),
     * => )
     * ```
     */
    public function testGroupMap(): void
    {
        $actual = HashMap::collect([
            '10-1' => ['id' => 10, 'sum' => 10],
            '10-2' => ['id' => 10, 'sum' => 15],
            '10-3' => ['id' => 10, 'sum' => 20],
            '20-1' => ['id' => 20, 'sum' => 10],
            '20-2' => ['id' => 20, 'sum' => 15],
            '30-1' => ['id' => 30, 'sum' => 20],
        ])->groupMap(
            fn(array $a) => $a['id'],
            fn(array $a) => $a['sum'] + 1,
        );

        $expected = HashMap::collect([
            10 => NonEmptyHashMap::collectNonEmpty([
                '10-3' => 21,
                '10-2' => 16,
                '10-1' => 11,
            ]),
            20 => NonEmptyHashMap::collectNonEmpty([
                '20-2' => 16,
                '20-1' => 11,
            ]),
            30 => NonEmptyHashMap::collectNonEmpty([
                '30-1' => 21,
            ]),
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testGroupMapReduce(): void
    {
        $actual = HashMap::collect([
            '10-1' => ['id' => 10, 'sum' => 10],
            '10-2' => ['id' => 10, 'sum' => 15],
            '10-3' => ['id' => 10, 'sum' => 20],
            '20-1' => ['id' => 20, 'sum' => 10],
            '20-2' => ['id' => 20, 'sum' => 15],
            '30-1' => ['id' => 30, 'sum' => 20],
        ])->groupMapReduce(
            fn(array $a) => $a['id'],
            fn(array $a) => $a['sum'],
            fn(int $old, int $new) => $old + $new,
        );

        $expected = HashMap::collect([10 => 45, 20 => 25, 30 => 20]);

        $this->assertEquals($expected, $actual);
    }

    public function testKeys(): void
    {
        $hm = HashMap::collectPairs([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toList()
        );
    }

    public function testValues(): void
    {
        $hm = HashMap::collectPairs([['a', 22], ['b', 33]]);

        $this->assertEquals(
            [22, 33],
            $hm->values()->toList()
        );
    }
}
