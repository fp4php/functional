<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Map;

use Fp\Collections\HashMap;
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

        $this->assertEquals([['b', 2], ['c', 3]], $hm->toArray());
    }

    public function testEvery(): void
    {
        $hm = HashMap::collect(['a' => 0, 'b' => 1]);

        $this->assertTrue($hm->every(fn($entry) => $entry->value >= 0));
        $this->assertFalse($hm->every(fn($entry) => $entry->value > 0));
        $this->assertTrue($hm->every(fn($entry) => in_array($entry->key, ['a', 'b'])));
    }

    public function testEveryMap(): void
    {
        $hm = HashMap::collect([
            'a' => new Foo(1),
            'b' => new Foo(2),
        ]);

        $this->assertEquals(
            Option::some($hm),
            $hm->traverseOption(fn($x) => $x->value->a >= 1 ? Option::some($x->value) : Option::none())
        );
        $this->assertEquals(
            Option::none(),
            $hm->traverseOption(fn($x) => $x->value->a >= 2 ? Option::some($x->value) : Option::none())
        );
    }

    public function testFilter(): void
    {
        $hm = HashMap::collect(['a' => new Foo(1), 'b' => 1, 'c' => new Foo(2)]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($e) => $e->value === 1)->toArray());
    }

    public function testFilterMap(): void
    {
        $this->assertEquals(
            [['b', 1], ['c', 2]],
            HashMap::collectPairs([['a', 'zero'], ['b', '1'], ['c', '2']])
                ->filterMap(fn($e) => is_numeric($e->value) ? Option::some((int) $e->value) : Option::none())
                ->toArray()
        );
    }

    public function testFlatMap(): void
    {
        $hm = HashMap::collectPairs([['2', 2], ['5', 5]]);

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]],
            $hm->flatMap(fn($e) => [
                [$e->value - 1, $e->value - 1],
                [$e->value, $e->value],
                [$e->value + 1, $e->value + 1]
            ])->toArray()
        );

        $this->assertEquals(
            [['2', 20], ['5', 5]],
            $hm->flatMap(fn($e) => [['2', 20], [$e->key, $e->value]])->toArray()
        );
    }

    public function testFold(): void
    {
        /** @var HashMap<string, int> $hm */
        $hm = HashMap::collectPairs([['2', 2], ['3', 3]]);

        $this->assertEquals(
            6,
            $hm->fold(1, fn(int $acc, $cur) => $acc + $cur->value)
        );
    }

    public function testMap(): void
    {
        $hm = HashMap::collectPairs([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', 'val-22'], ['3', 'val-33']],
            $hm->map(fn($e) => "val-{$e}")->toArray()
        );

        $this->assertEquals(
            [['2', 'key-2-val-22'], ['3', 'key-3-val-33']],
            $hm->mapWithKey(fn($key, $elem) => "key-{$key}-val-{$elem}")->toArray()
        );

        $this->assertEquals(
            [[22, 22], [33, 33]],
            $hm->mapKeys(fn($e) => $e->value)->toArray()
        );
    }

    public function testKeys(): void
    {
        $hm = HashMap::collectPairs([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toArray()
        );
    }

    public function testValues(): void
    {
        $hm = HashMap::collectPairs([['a', 22], ['b', 33]]);

        $this->assertEquals(
            [22, 33],
            $hm->values()->toArray()
        );
    }
}
