<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;
use function Fp\Collection\isSequence;

final class IsSequenceTest extends TestCase
{
    public function testIsAscendingSequence(): void
    {
        $this->assertTrue(isSequence([0, 1, 2, 3], 0, 'ASC'));
        $this->assertFalse(isSequence([0, 1, 3], 0, 'ASC'));
        $this->assertFalse(isSequence([1, 2, 3], 0, 'ASC'));
        $this->assertFalse(isSequence([-1, 0, 1], 0, 'ASC'));
    }

    public function testIsDescendingSequence(): void
    {
        $this->assertTrue(isSequence([3, 2, 1, 0], 3, 'DESC'));
//        $this->assertFalse(isSequence([0, 1, 3], 0, 'DESC'));
//        $this->assertFalse(isSequence([1, 2, 3], 0, 'DESC'));
//        $this->assertFalse(isSequence([-1, 0, 1], 0, 'DESC'));
    }
}
