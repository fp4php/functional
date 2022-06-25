<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySeq;

use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptySeqCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collect([1, 2, 3])->getUnsafe()->toList());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collect([1, 2, 3])->getUnsafe()->toList());

        $this->assertTrue(Option::try(fn() => NonEmptyArrayList::collectUnsafe([]))->isNone());
        $this->assertTrue(Option::try(fn() => NonEmptyLinkedList::collectUnsafe([]))->isNone());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collectUnsafe([1, 2, 3])->toList());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collectUnsafe([1, 2, 3])->toList());

        $this->assertTrue(Option::try(fn() => NonEmptyArrayList::collectUnsafe([]))->isNone());
        $this->assertTrue(Option::try(fn() => NonEmptyLinkedList::collectUnsafe([]))->isNone());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collectNonEmpty([1, 2, 3])->toList());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->toList());
    }

    public function testCollectOption(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyArrayList::collect([1, 2, 3])->getUnsafe()->toList());
        $this->assertEquals([1, 2, 3], NonEmptyLinkedList::collect([1, 2, 3])->getUnsafe()->toList());

        $this->assertNull(NonEmptyArrayList::collect([])->get());
        $this->assertNull(NonEmptyLinkedList::collect([])->get());
    }
}
