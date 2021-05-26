<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\exists;

final class ExistsTest extends TestCase
{
    public function testExists(): void
    {
        $c = [1, 2, 3];

        $this->assertTrue(exists($c, 2));
        $this->assertFalse(exists($c, 4));
        $this->assertTrue(exists($c, fn (int $v) => $v === 3));
        $this->assertFalse(exists($c, fn (int $v) => $v === 4));
    }
}
