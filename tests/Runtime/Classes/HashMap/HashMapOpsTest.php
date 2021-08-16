<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashMap;

use Fp\Collections\HashMap;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

final class HashMapOpsTest extends TestCase
{
    public function testGet(): void
    {
        $hm = HashMap::collectIterable(['a' => 1, 'b' => 2]);

        $this->assertEquals(2, $hm->get('b')->get());
        $this->assertEquals(2, $hm('b')->get());
    }

    public function testUpdatedAndRemoved(): void
    {
        $hm = HashMap::collectIterable(['a' => 1, 'b' => 2]);
        $hm = $hm->updated('c', 3);
        $hm = $hm->removed('a');

        $this->assertEquals([['b', 2], ['c', 3]], $hm->toArray());
    }

    public function testEvery(): void
    {
        $hm = HashMap::collectIterable(['a' => 0, 'b' => 1]);

        $this->assertTrue($hm->every(fn($i) => $i >= 0));
        $this->assertFalse($hm->every(fn($i) => $i > 0));
        $this->assertTrue($hm->every(fn($i, $k) => in_array($k, ['a', 'b'])));
    }

    public function testFilter(): void
    {
        $hm = HashMap::collectIterable(['a' => new Foo(1), 'b' => 1, 'c' => new Foo(2)]);
        $this->assertEquals([['b', 1]], $hm->filter(fn($i) => $i === 1)->toArray());
    }

    public function testFlatMap(): void
    {
        $hm = HashMap::collect([['2', 2], ['5', 5]]);

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]],
            $hm->flatMap(fn($e, $k) => [[$e - 1, $e - 1], [$e, $e], [$e + 1, $e + 1]])->toArray()
        );

        $this->assertEquals(
            [['2', 20], ['5', 5]],
            $hm->flatMap(fn($e, $k) => [['2', 20], [$k, $e]])->toArray()
        );
    }

    public function testFold(): void
    {
        /** @var HashMap<string, int> $hm */
        $hm = HashMap::collect([['2', 2], ['3', 3]]);

        $this->assertEquals(
            ['123', 6],
            $hm->fold(['1', 1], fn(array $acc, array $cur): array => [$acc[0] . $cur[0], $acc[1] + $cur[1]])
        );
    }

    public function testReduce(): void
    {
        /** @var HashMap<string, int> $hm */
        $hm = HashMap::collect([['2', 2], ['3', 3]]);

        $this->assertEquals(
            ['123', 6],
            $hm->reduce(fn(array $acc, array $cur): array => [$acc[0] . $cur[0], $acc[1] + $cur[1]])->get()
        );
    }

    public function testMap(): void
    {
        $hm = HashMap::collect([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [['2', '2'], ['3', '3']],
            $hm->map(fn($e, $k) => $k)->toArray()
        );
    }

    public function testReindex(): void
    {
        $hm = HashMap::collect([['2', 22], ['3', 33]]);

        $this->assertEquals(
            [[22, 22], [33, 33]],
            $hm->reindex(fn($e, $k) => $e)->toArray()
        );
    }

    public function testKeys(): void
    {
        $hm = HashMap::collect([['a', 22], ['b', 33]]);

        $this->assertEquals(
            ['a', 'b'],
            $hm->keys()->toArray()
        );
    }
}
