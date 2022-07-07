<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Map;

use Fp\Collections\HashMap;
use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
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
        /** @var HashMap<string, int> $hm */
        $hm = HashMap::collectPairs([['2', 2], ['3', 3]]);

        $this->assertEquals(
            6,
            $hm->fold(1, fn(int $acc, $cur) => $acc + $cur)
        );
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
