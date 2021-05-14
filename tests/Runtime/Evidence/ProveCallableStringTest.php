<?php

declare(strict_types=1);

namespace Tests\Runtime\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use function Fp\Evidence\proveCallableString;

final class ProveCallableStringTest extends TestCase
{
    public function testProveCallableString(): void
    {
        $this->assertInstanceOf(None::class, proveCallableString('string'));
        $this->assertInstanceOf(Some::class, proveCallableString('array_map'));
    }
}
