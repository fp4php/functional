<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveInt;

final class ProveIntTest extends TestCase
{
    public function testProveInt(): void
    {
        $this->assertInstanceOf(None::class, proveInt(true));
        $this->assertInstanceOf(None::class, proveInt(false));

        $this->assertInstanceOf(Some::class, proveInt(1));
        $this->assertInstanceOf(Some::class, proveInt(0));

        $this->assertInstanceOf(None::class, proveInt(1.1));
        $this->assertInstanceOf(None::class, proveInt('string'));
    }
}
