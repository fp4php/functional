<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\LinkedList;

use Fp\Collections\LinkedList;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Cast\asList;

final class LinkedListOpsTest extends TestCase
{
    public function testAppendPrepend(): void
    {
        $linkedList = LinkedList::collect([1, 2, 3]);
        $linkedList = $linkedList->prepend(0)->append(4);

        $list = asList($linkedList);

        $this->assertEquals(
            [0, 1, 2, 3, 4],
            $list,
        );
    }

    public function testAnyOf(): void
    {
        $linkedList = LinkedList::collect([1, new Foo(1)]);

        $this->assertTrue($linkedList->anyOf(Foo::class));
        $this->assertFalse($linkedList->anyOf(Bar::class));
    }

    public function testAt(): void
    {
        $linkedList = LinkedList::collect([0, 1, 2, 3, 4, 5]);

        $this->assertEquals(0, $linkedList->at(0)->getUnsafe());
        $this->assertEquals(3, $linkedList->at(3)->getUnsafe());
        $this->assertEquals(5, $linkedList->at(5)->getUnsafe());
    }

    public function testEvery(): void
    {
        $linkedList = LinkedList::collect([0, 1, 2, 3, 4, 5]);

        $this->assertTrue($linkedList->every(fn($i) => $i >= 0));
        $this->assertFalse($linkedList->every(fn($i) => $i > 0));
    }

    public function testEveryOf(): void
    {
        $linkedList0 = LinkedList::collect([new Foo(1), new Foo(1)]);
        $linkedList1 = LinkedList::collect([new Bar(true), new Foo(1)]);

        $this->assertTrue($linkedList0->everyOf(Foo::class));
        $this->assertFalse($linkedList1->everyOf(Foo::class));
    }
}
