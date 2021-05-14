<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\isSequence;

final class IsSequenceTest extends TestCase
{
    public function testIsAscendingSequence(): void
    {
        $this->assertTrue(isSequence([0, 1, 2, 3]));
        $this->assertFalse(isSequence([0, 1, 3]));
        $this->assertFalse(isSequence([1, 2, 3]));
        $this->assertFalse(isSequence([-1, 0, 1]));
    }

    public function testIsDescendingSequence(): void
    {
        $this->assertTrue(isSequence([3, 2, 1, 0], 3, 'DESC'));
        $this->assertFalse(isSequence([3, 1, 0], 3, 'DESC'));
        $this->assertFalse(isSequence([4, 3, 2, 1, 0], 3, 'DESC'));
        $this->assertFalse(isSequence([2, 1, 0], 3, 'DESC'));
        $this->assertTrue(isSequence([0, -1, -2], 0, 'DESC'));
    }
}
