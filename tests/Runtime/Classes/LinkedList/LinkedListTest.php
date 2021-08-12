<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\LinkedList;

use Fp\Collections\LinkedList;
use PHPUnit\Framework\TestCase;

use function Fp\Cast\asList;

final class LinkedListTest extends TestCase
{
    public function testCollect(): void
    {
        $linkedList =  LinkedList::collect([1, 2, 3]);

        $list = asList($linkedList);

        $this->assertEquals(
            [1, 2, 3],
            $list,
        );
    }
}
