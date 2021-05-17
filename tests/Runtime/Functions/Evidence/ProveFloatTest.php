<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveFloat;

final class ProveFloatTest extends TestCase
{
    public function testProveFloat(): void
    {
        $this->assertInstanceOf(None::class, proveFloat(true));
        $this->assertInstanceOf(None::class, proveFloat(false));

        $this->assertInstanceOf(None::class, proveFloat(1));
        $this->assertInstanceOf(None::class, proveFloat(0));

        $this->assertInstanceOf(Some::class, proveFloat(1.1));
        $this->assertInstanceOf(Some::class, proveFloat(0.0));

        $this->assertInstanceOf(None::class, proveFloat('string'));
    }
}
