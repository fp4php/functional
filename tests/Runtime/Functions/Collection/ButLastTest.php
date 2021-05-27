<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\butLast;

final class ButLastTest extends TestCase
{
    public function testAtWithArray(): void
    {
        $this->assertEquals(['a' => true, 1], butLast(['a' => true, 1, 2]));
        $this->assertEquals([], butLast([1]));
        $this->assertEquals([], butLast([]));
    }
}
