<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;

use function Fp\Collection\isNonEmptySequence;
use function Fp\Collection\isSequence;

final class IsSequenceTest extends TestCase
{
    public function testIsSequence()
    {
        $this->assertTrue(isSequence([]));
        $this->assertFalse(isNonEmptySequence([]));
    }

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

    public function testIsAscendingNonEmptySequence(): void
    {
        $this->assertTrue(isNonEmptySequence([0, 1, 2, 3]));
        $this->assertFalse(isNonEmptySequence([0, 1, 3]));
        $this->assertFalse(isNonEmptySequence([1, 2, 3]));
        $this->assertFalse(isNonEmptySequence([-1, 0, 1]));
    }

    public function testIsDescendingNonEmptySequence(): void
    {
        $this->assertTrue(isNonEmptySequence([3, 2, 1, 0], 3, 'DESC'));
        $this->assertFalse(isNonEmptySequence([3, 1, 0], 3, 'DESC'));
        $this->assertFalse(isNonEmptySequence([4, 3, 2, 1, 0], 3, 'DESC'));
        $this->assertFalse(isNonEmptySequence([2, 1, 0], 3, 'DESC'));
        $this->assertTrue(isNonEmptySequence([0, -1, -2], 0, 'DESC'));
    }
}
