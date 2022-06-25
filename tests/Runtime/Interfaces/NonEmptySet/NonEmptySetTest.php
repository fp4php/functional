<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySet;

use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptySet;
use Generator;
use PHPUnit\Framework\TestCase;

final class NonEmptySetTest extends TestCase
{
    public function provideTestCastsData(): Generator
    {
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 3, 3]), [1, 2, 3]];
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(NonEmptySet $set, array $expected): void
    {
        $this->assertEquals($expected, $set->toArray());
        $this->assertEquals($expected, $set->toNonEmptyArray());
        $this->assertEquals($expected, $set->toLinkedList()->toArray());
        $this->assertEquals($expected, $set->toNonEmptyLinkedList()->toArray());
        $this->assertEquals($expected, $set->toArrayList()->toArray());
        $this->assertEquals($expected, $set->toNonEmptyArrayList()->toArray());
        $this->assertEquals($expected, $set->toHashSet()->toArray());
        $this->assertEquals($expected, $set->toNonEmptyHashSet()->toArray());
    }

    public function testCastToHashMap(): void
    {
        $set = NonEmptyHashSet::collectNonEmpty([
            ['fst', 1],
            ['snd', 2],
            ['snd', 2],
            ['thd', 3],
            ['thd', 3],
        ]);

        $this->assertEquals([['fst', 1], ['snd', 2], ['thd', 3]], $set->toHashMap()->toArray());
        $this->assertEquals([['fst', 1], ['snd', 2], ['thd', 3]], $set->toNonEmptyHashMap()->toArray());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySet $set): void
    {
        $this->assertEquals(3, $set->count());
    }
}
