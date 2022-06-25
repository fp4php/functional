<?php

declare(strict_types=1);

namespace Tests\Runtime\Interfaces\Seq;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\Nil;
use PHPUnit\Framework\TestCase;

final class SeqCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $this->assertTrue(Nil::getInstance() === Nil::getInstance());
        $this->assertEquals([1, 2, 3], ArrayList::collect([1, 2, 3])->toList());
        $this->assertEquals([1, 2, 3], LinkedList::collect([1, 2, 3])->toList());
    }

    public function testSingleton(): void
    {
        $this->assertEquals([1], ArrayList::singleton(1)->toList());
        $this->assertEquals([1], LinkedList::singleton(1)->toList());
    }

    public function testEmpty(): void
    {
        $this->assertEquals([], ArrayList::empty()->toList());
        $this->assertEquals([], LinkedList::empty()->toList());
    }

    public function testRange(): void
    {
        $this->assertEquals([], ArrayList::range(0, 0)->toList());
        $this->assertEquals([], LinkedList::range(0, 0)->toList());

        $this->assertEquals([0, 1, 2], ArrayList::range(0, 3)->toList());
        $this->assertEquals([0, 1, 2], LinkedList::range(0, 3)->toList());

        $this->assertEquals([0, 2], ArrayList::range(0, 3, 2)->toList());
        $this->assertEquals([0, 2], LinkedList::range(0, 3, 2)->toList());
    }
}
