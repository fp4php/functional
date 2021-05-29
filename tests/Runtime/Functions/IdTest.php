<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions;

use PHPUnit\Framework\TestCase;

use function Fp\id;

final class IdTest extends TestCase
{
    public function testId(): void
    {
        $this->assertTrue(id(true));
        $this->assertFalse(id(false));
    }
}
