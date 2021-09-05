<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashMap;

use Fp\Collections\NonEmptyHashMap;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

final class NonEmptyHashMapOpsTest extends TestCase
{
    public function testGet(): void
    {
        $hm = NonEmptyHashMap::collectIterableUnsafe(['a' => 1, 'b' => 2]);

        $this->assertEquals(2, $hm->get('b')->get());
        $this->assertEquals(2, $hm('b')->get());
    }

    public function testUpdatedAndRemoved(): void
    {
        $hm = NonEmptyHashMap::collectIterableUnsafe(['a' => 1, 'b' => 2]);
        $hm = $hm->updated('c', 3);
        $hm = $hm->removed('a');

        $this->assertEquals([['b', 2], ['c', 3]], $hm->toArray());
    }

    public function testEvery(): void
    {
        $hm = NonEmptyHashMap::collectIterableUnsafe(['a' => 0, 'b' => 1]);

        $this->assertTrue($hm->every(fn($entry) => $entry->value >= 0));
        $this->assertFalse($hm->every(fn($entry) => $entry->value > 0));
        $this->assertTrue($hm->every(fn($entry) => in_array($entry->key, ['a', 'b'])));
    }

    public function testFilter(): void
    {
        $hm = NonEmptyHashMap::collectIterableUnsafe(['a' => new Foo(1), 'b' => 1, 'c' => new Foo(2)]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($e) => $e->value === 1)->toArray());
    }


    public function testMap(): void
    {
        $hm = NonEmptyHashMap::collectNonEmpty([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', '2'], ['3', '3']],
            $hm->map(fn($e) => $e->key)->toArray()
        );
    }

    public function testReindex(): void
    {
        $hm = NonEmptyHashMap::collectNonEmpty([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [[22, 22], [33, 33]],
            $hm->reindex(fn($e) => $e->value)->toArray()
        );
    }

    public function testKeys(): void
    {
        $hm = NonEmptyHashMap::collectNonEmpty([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toArray()
        );
    }

    public function testValues(): void
    {
        $hm = NonEmptyHashMap::collectNonEmpty([['a', 22], ['b', 33]]);

        $this->assertEquals(
            [22, 33],
            $hm->values()->toArray()
        );
    }
}
