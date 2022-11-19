<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Separated;

use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\LinkedListBuffer;
use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;
use PHPUnit\Framework\TestCase;

final class SeparatedTest extends TestCase
{
    public function testToTuple(): void
    {
        $this->assertEquals([1, 2], (Separated::create(1, 2))->toTuple());
    }

    public function testMap(): void
    {
        $this->assertEquals(
            Separated::create(1, 3),
            Separated::create(1, 2)->map(fn($i) => $i + 1),
        );
    }

    public function testMapLeft(): void
    {
        $this->assertEquals(
            Separated::create(2, 2),
            Separated::create(1, 2)->mapLeft(fn($i) => $i + 1),
        );
    }

    public function testTap(): void
    {
        /** @var LinkedListBuffer<int> $buffer */
        $buffer = new LinkedListBuffer();

        $this->assertEquals(
            Separated::create(1, 2),
            Separated::create(1, 2)->tap(fn($i) => $buffer->append($i)),
        );

        $this->assertEquals(
            LinkedList::singleton(2),
            $buffer->toLinkedList(),
        );
    }

    public function testTapLeft(): void
    {
        /** @var LinkedListBuffer<int> $buffer */
        $buffer = new LinkedListBuffer();

        $this->assertEquals(
            Separated::create(1, 2),
            Separated::create(1, 2)->tapLeft(fn($i) => $buffer->append($i)),
        );

        $this->assertEquals(
            LinkedList::singleton(1),
            $buffer->toLinkedList(),
        );
    }

    public function testSwap(): void
    {
        $this->assertEquals(
            Separated::create(2, 1),
            Separated::create(1, 2)->swap(),
        );
    }

    public function testGetLeft(): void
    {
        $this->assertEquals(
            1,
            Separated::create(1, 2)->getLeft(),
        );
    }

    public function testGetRight(): void
    {
        $this->assertEquals(
            2,
            Separated::create(1, 2)->getRight(),
        );
    }

    public function testToEither(): void
    {
        $this->assertEquals(
            Either::right(ArrayList::collect([1, 2, 3])),
            Separated::create(ArrayList::collect([]), ArrayList::collect([1, 2, 3]))->toEither(),
        );

        $this->assertEquals(
            Either::left(ArrayList::collect([4, 5, 6])),
            Separated::create(ArrayList::collect([4, 5, 6]), ArrayList::collect([1, 2, 3]))->toEither(),
        );
    }

    public function testToString(): void
    {
        $this->assertEquals('Separated(1, 2)', Separated::create(1, 2)->toString());
    }
}
