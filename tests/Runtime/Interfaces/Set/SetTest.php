<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Set;

use Fp\Collections\HashSet;
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
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 2, 3, 3])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3]],
            HashSet::collect([1, 2, 3])->toHashMap(fn($e) => [$e, $e])->toArray(),
        );
    }

    public function testCount(): void
    {
        $this->assertCount(3, HashSet::collect([1, 2, 3]));
    }
}
