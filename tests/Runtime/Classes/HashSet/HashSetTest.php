<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\HashSet;

use Fp\Collections\HashSet;
use PHPUnit\Framework\TestCase;

final class HashSetTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            HashSet::collect([1, 1, 2, 3, 3])->toArray(),
        );
    }

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
    }
}
