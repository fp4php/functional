<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\LinkedList;

use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyLinkedList;
use PHPUnit\Framework\TestCase;

use function Fp\Cast\asList;

final class LinkedListTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            LinkedList::collect([1, 2, 3])->toArray(),
        );
    }

    public function testCasts(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            LinkedList::collect([1, 2, 3])->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            LinkedList::collect([1, 2, 3])->toLinkedList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            LinkedList::collect([1, 2, 3])->toArrayList()->toArray(),
        );

        $this->assertEquals(
            [1, 2, 3],
            LinkedList::collect([1, 2, 3])->toHashSet()->toArray(),
        );

        $this->assertEquals(
            [[1, 1], [2, 2], [3, 3]],
            LinkedList::collect([1, 2, 3])->toHashMap(fn($e) => [$e, $e])->toArray(),
        );
    }

    public function testCount(): void
    {
        $this->assertEquals(3, LinkedList::collect([1, 2, 3])->count());
    }
}
