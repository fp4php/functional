<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Evidence\proveObject;

final class ProveObjectTest extends TestCase
{
    public function testProveObject(): void
    {
        $this->assertInstanceOf(Some::class, proveObject(new Foo(1)));
        $this->assertInstanceOf(None::class, proveObject(1));
        $this->assertEquals(1, proveObject(new Foo(1))->getUnsafe()->a);
    }
}
