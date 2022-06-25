<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptyMap;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
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
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', 0], ['b', 1]]);

        $this->assertTrue($hm->every(fn($entry) => $entry >= 0));
        $this->assertFalse($hm->every(fn($entry) => $entry > 0));
    }

    public function testEveryMap(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([
            ['a', new Foo(1)],
            ['b', new Foo(2)],
        ]);

        $this->assertEquals(
            Option::some($hm),
            $hm->traverseOption(fn($x) => $x->a >= 1 ? Option::some($x) : Option::none())
        );
        $this->assertEquals(
            Option::none(),
            $hm->traverseOption(fn($x) => $x->a >= 2 ? Option::some($x) : Option::none())
        );
    }

    public function testFilter(): void
    {
        $hm = NonEmptyHashMap::collectPairsUnsafe([['a', new Foo(1)], ['b', 1], ['c',  new Foo(2)]]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($e) => $e === 1)->toList());
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

    public function testMap(): void
    {
        $hm = NonEmptyHashMap::collectPairsNonEmpty([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', 23], ['3', 34]],
            $hm->map(fn($e) => $e + 1)->toList()
        );

        $this->assertEquals(
            [['2', '2-22'], ['3', '3-33']],
            $hm->mapWithKey(fn($key, $elem) => "{$key}-{$elem}")->toList()
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
            $hm->reindexWithKey(fn($k, $v) => "{$k}-{$v}")->toList(),
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
}
