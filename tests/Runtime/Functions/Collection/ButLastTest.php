<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\butLast;
use function Fp\Collection\last;

final class ButLastTest extends TestCase
{
    public function testButLast(): void
    {
        $this->assertEquals([], butLast([]));
        $this->assertEquals([], butLast([1]));
        $this->assertEquals([1, 2], butLast([1, 2, 3]));
    }
}
