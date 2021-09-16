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
        yield NonEmptyHashSet::class => [NonEmptyHashSet::collectNonEmpty([1, 2, 3, 3])];
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCasts(NonEmptySet $set): void
    {
        $this->assertEquals([1, 2, 3], $set->toArray());
        $this->assertEquals([1, 2, 3], $set->toLinkedList()->toArray());
        $this->assertEquals([1, 2, 3], $set->toNonEmptyLinkedList()->toArray());
        $this->assertEquals([1, 2, 3], $set->toArrayList()->toArray());
        $this->assertEquals([1, 2, 3], $set->toNonEmptyArrayList()->toArray());
        $this->assertEquals([1, 2, 3], $set->toHashSet()->toArray());
        $this->assertEquals([1, 2, 3], $set->toNonEmptyHashSet()->toArray());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $set->toHashMap(fn($e) => [$e, $e])->toArray());
        $this->assertEquals([[1, 1], [2, 2], [3, 3]], $set->toNonEmptyHashMap(fn($e) => [$e, $e])->toArray());
    }

    /**
     * @dataProvider provideTestCastsData
     */
    public function testCount(NonEmptySet $set): void
    {
        $this->assertEquals(3, $set->count());
    }
}
