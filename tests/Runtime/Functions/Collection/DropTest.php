<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\drop;
use function Fp\Collection\dropRight;
use function Fp\Collection\dropWhile;

final class DropTest extends TestCase
{
    public function testDrop(): void
    {
        $this->assertEquals([3], drop([1, 2, 3], length: 2));
        $this->assertEquals([], drop([], length: 2));
    }

    public function testDropRight(): void
    {
        $this->assertEquals([1], dropRight([1, 2, 3], length: 2));
        $this->assertEquals([], dropRight([], length: 2));
    }

    public function testDropWhile(): void
    {
        $this->assertEquals([4, 5], dropWhile([1, 2, 3, 4, 5], fn($e) => $e < 3));
        $this->assertEquals([], dropWhile([], fn($e) => $e < 3));
    }
}
