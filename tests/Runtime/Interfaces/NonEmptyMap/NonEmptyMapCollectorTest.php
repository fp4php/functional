<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptyMap;

use Fp\Collections\NonEmptyHashMap;
use Fp\Functional\Option\Option;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertNull(NonEmptyHashMap::collect([])->get()?->toArray());
        $this->assertEquals([['a', 1]], NonEmptyHashMap::collect(['a' => 1])->get()?->toArray());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertNull(Option::try(fn() => NonEmptyHashMap::collectUnsafe([]))->get());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals(
            [['a', 1]],
            NonEmptyHashMap::collectNonEmpty(['a' => 1])->toArray()
        );
    }

    public function provideTestCollectPairsData(): Generator
    {
        yield NonEmptyHashMap::class => [
            NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]]),
            NonEmptyHashMap::collectPairs([]),
        ];
    }

    /**
     * @dataProvider provideTestCollectPairsData
     * @param Option<NonEmptyHashMap> $m1
     * @param Option<NonEmptyHashMap> $m2
     */
    public function testCollectPairs(Option $m1, Option $m2): void
    {
        $expected = [['a', 1], ['b', 2]];
        $this->assertEquals($expected, $m1->getUnsafe()->toArray());
        $this->assertNull($m2->get());
    }

    public function provideTestCollectPairsUnsafeData(): Generator
    {
        yield NonEmptyHashMap::class => [
            fn() => NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]),
            fn() => NonEmptyHashMap::collectPairsUnsafe([])
        ];
    }

    /**
     * @dataProvider provideTestCollectPairsUnsafeData
     * @param callable(): NonEmptyHashMap $t1
     * @param callable(): NonEmptyHashMap $t2
     */
    public function testCollectPairsUnsafe(callable $t1, callable $t2): void
    {
        $expected = [['a', 1], ['b', 2]];
        $this->assertEquals($expected, Option::try(fn() => $t1()->toArray())->get());
        $this->assertNull(Option::try(fn() => $t2()->toArray())->get());
    }
}
