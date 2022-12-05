<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\contains;
use function Fp\Collection\containsT;

final class ContainsTest extends TestCase
{
    public function testContains(): void
    {
        $this->assertTrue(contains(42, [40, 41, 42]));
        $this->assertFalse(contains(43, [40, 41, 42]));
        $this->assertTrue(containsT(42, 40, 41, 42));
        $this->assertFalse(containsT(43, 40, 41, 42));
    }
}
