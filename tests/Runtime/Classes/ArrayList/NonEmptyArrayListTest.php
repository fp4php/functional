<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\ArrayList;

use Fp\Collections\EmptyCollectionException;
use Fp\Collections\NonEmptyArrayList;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptyArrayListTest extends TestCase
{
    /**
     * @throws EmptyCollectionException
     */
    public function testCollect(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collect([1, 2, 3])->toArray(),
        );

        $catch = Option::try(fn() => NonEmptyArrayList::collect([]));
        $this->assertTrue($catch->isNone());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectUnsafe([1, 2, 3])->toArray(),
        );

        $catch = Option::try(fn() => NonEmptyArrayList::collectUnsafe([]));
        $this->assertTrue($catch->isNone());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toArray(),
        );
    }

    public function testCollectOption(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectOption([1, 2, 3])->getUnsafe()->toArray(),
        );

        $this->assertNull(NonEmptyArrayList::collectOption([])->get());
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toNonEmptyLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toNonEmptyArrayList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toNonEmptyHashSet()->toArray(),
        );
    }

    public function testCount(): void
    {
        $this->assertEquals(3, NonEmptyArrayList::collectNonEmpty([1, 2, 3])->count());
    }

    public function testSorted(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyArrayList::collectNonEmpty([3, 2, 1])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toArray()
        );

        $this->assertEquals(
            [3, 2, 1],
            NonEmptyArrayList::collectNonEmpty([3, 2, 1])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toArray()
        );
    }
}
