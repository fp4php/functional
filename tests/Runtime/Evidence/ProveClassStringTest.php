<?php

declare(strict_types=1);

namespace Tests\Runtime\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Foo;

use function Fp\Evidence\proveClassString;

final class ProveClassStringTest extends TestCase
{
    public function testProveClassString(): void
    {
        $this->assertInstanceOf(None::class, proveClassString('string'));
        $this->assertInstanceOf(None::class, proveClassString('array_map'));
        $this->assertInstanceOf(Some::class, proveClassString(Foo::class));
    }
}
