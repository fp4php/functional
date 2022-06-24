<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Set;

use Fp\Collections\HashSet;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class SetTest extends TestCase
{
    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toArray(),
        );

        $this->assertEquals(
            Option::some([1, 2, 3]),
            HashSet::collect([1, 2, 3, 3])->toNonEmptyArray(),
        );

        $this->assertEquals(
            Option::none(),
            HashSet::collect([])->toNonEmptyArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            Option::some(NonEmptyLinkedList::collectNonEmpty([1, 2, 3])),
            HashSet::collect([1, 2, 3, 3])->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            Option::none(),
            HashSet::collect([])->toNonEmptyLinkedList(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            Option::some(NonEmptyArrayList::collectNonEmpty([1, 2, 3])),
            HashSet::collect([1, 2, 3, 3])->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            Option::none(),
            HashSet::collect([])->toNonEmptyArrayList(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashSet::collectNonEmpty([1, 2, 3])),
            HashSet::collect([1, 2, 3, 3])->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            Option::none(),
            HashSet::collect([])->toNonEmptyHashSet(),
        );

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3]],
            HashSet::collect([1, 2, 3])->toHashMap(fn($e) => [$e, $e])->toArray(),
        );

        $this->assertEquals(
            Option::some(NonEmptyHashMap::collectPairsNonEmpty([[1, 1], [2, 2], [3, 3]])),
            HashSet::collect([1, 2, 3])->toNonEmptyHashMap(fn($e) => [$e, $e]),
        );

        $this->assertEquals(
            Option::none(),
            HashSet::collect([])->toNonEmptyHashMap(fn($e) => [$e, $e]),
        );
    }

    public function testCount(): void
    {
        $this->assertCount(3, HashSet::collect([1, 2, 3]));
    }
}
