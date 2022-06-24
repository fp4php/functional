<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptyMap;

use Fp\Collections\NonEmptyHashMap;
use PHPUnit\Framework\TestCase;

final class NonEmptyMapTest extends TestCase
{
    public function testCasts(): void
    {
        $expected = [['a', 1], ['b', 2]];

        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toLinkedList()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyLinkedList()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toArrayList()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyArrayList()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toHashSet()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyHashSet()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toHashMap()->toArray());
        $this->assertEquals($expected, NonEmptyHashMap::collectPairsNonEmpty($expected)->toNonEmptyHashMap()->toArray());
    }

    public function testCount(): void
    {
        $this->assertCount(2, NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]]));
    }
}
