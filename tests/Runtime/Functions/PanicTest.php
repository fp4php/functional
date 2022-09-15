<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions;

use RuntimeException;
use PHPUnit\Framework\TestCase;

use function Fp\panic;

final class PanicTest extends TestCase
{
    public function testPanic(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Critical system error');

        panic('Critical system error')();
    }

    public function testPanicInterpolation(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Critical system error: 42');

        panic('Critical system error: %s', 42)();
    }
}
