<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveNull;

final class ProveNullTest extends TestCase
{
    public function testProveNull(): void
    {
        $this->assertEquals(Option::none(), proveNull(1));
        $this->assertEquals(Option::some(null), proveNull(null));
    }
}
