<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\NonEmptySet;

use Fp\Collections\NonEmptyHashSet;
use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

final class NonEmptySetCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyHashSet::collect([1, 2, 3])->getUnsafe()->toArray());
        $this->assertTrue(Option::try(fn() => NonEmptyHashSet::collectUnsafe([]))->isNone());
    }

    public function testCollectUnsafe(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyHashSet::collectUnsafe([1, 2, 3])->toArray());
        $this->assertTrue(Option::try(fn() => NonEmptyHashSet::collectUnsafe([]))->isNone());
    }

    public function testCollectNonEmpty(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            NonEmptyHashSet::collectNonEmpty([1, 2, 3])->toArray(),
        );
    }

    public function testCollectOption(): void
    {
        $this->assertEquals([1, 2, 3], NonEmptyHashSet::collect([1, 2, 3])->getUnsafe()->toArray());
        $this->assertNull(NonEmptyHashSet::collect([])->get());
    }
}
