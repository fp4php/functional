<?php

declare(strict_types=1);

namespace Tests\Runtime\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveString;

final class ProveStringTest extends TestCase
{
    public function testProveString(): void
    {
        $this->assertInstanceOf(None::class, proveString(true));
        $this->assertInstanceOf(None::class, proveString(false));

        $this->assertInstanceOf(None::class, proveString(1));
        $this->assertInstanceOf(None::class, proveString(0));

        $this->assertInstanceOf(None::class, proveString(1.1));
        $this->assertInstanceOf(None::class, proveString(0.0));

        $this->assertInstanceOf(Some::class, proveString('string'));
    }
}
