<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\LinkedList;

use Fp\Collections\EmptyCollectionException;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptyLinkedListTest extends TestCase
{
    /**
     * @throws EmptyCollectionException
     */
    public function testCollect(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collect([1, 2, 3])->toArray(),
        );

        $catch = Option::try(fn() => NonEmptyLinkedList::collect([]));
        $this->assertTrue($catch->isNone());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectUnsafe([1, 2, 3])->toArray(),
        );

        $catch = Option::try(fn() => NonEmptyLinkedList::collectUnsafe([]));
        $this->assertTrue($catch->isNone());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toArray(),
        );
    }

    public function testCollectOption(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectOption([1, 2, 3])->getUnsafe()->toArray(),
        );

        $this->assertNull(NonEmptyLinkedList::collectOption([])->get());
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toNonEmptyLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toNonEmptyArrayList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toNonEmptyHashSet()->toArray(),
        );

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3]],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toHashMap(fn($e) => [$e, $e])->toArray(),
        );

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3]],
            NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toNonEmptyHashMap(fn($e) => [$e, $e])->toArray(),
        );
    }

    public function testCount(): void
    {
        $this->assertEquals(3, NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->count());
    }

    public function testSorted(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyLinkedList::collectNonEmpty([3, 2, 1])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toArray()
        );

        $this->assertEquals(
            [3, 2, 1],
            NonEmptyLinkedList::collectNonEmpty([3, 2, 1])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toArray()
        );
    }
}
