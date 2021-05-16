<?php

declare(strict_types=1);

namespace Tests\Runtime\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveOf;

final class ProveOfTest extends TestCase
{
    public function testProveOf(): void
    {
        $this->assertInstanceOf(None::class, proveOf(true, Foo::class));
        $this->assertInstanceOf(None::class, proveOf(false, Foo::class));

        $this->assertInstanceOf(None::class, proveOf(1, Foo::class));
        $this->assertInstanceOf(None::class, proveOf(0, Foo::class));

        $this->assertInstanceOf(None::class, proveOf(1.1, Foo::class));
        $this->assertInstanceOf(None::class, proveOf('string', Foo::class));

        $this->assertInstanceOf(Some::class, proveOf(new Foo(1), Foo::class));
        $this->assertInstanceOf(None::class, proveOf(new Bar(true), Foo::class));
    }
}
