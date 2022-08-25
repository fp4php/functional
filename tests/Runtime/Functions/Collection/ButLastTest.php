<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\init;

final class ButLastTest extends TestCase
{
    public function testButLast(): void
    {
        $this->assertEquals([], init([]));
        $this->assertEquals([], init([1]));
        $this->assertEquals([1, 2], init([1, 2, 3]));
    }
}
