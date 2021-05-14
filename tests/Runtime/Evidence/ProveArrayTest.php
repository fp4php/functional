<?php

declare(strict_types=1);

namespace Tests\Runtime\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\FooIterable;

use function Fp\Evidence\proveArray;
use function Fp\Evidence\proveArrayOf;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyArrayOf;

final class ProveArrayTest extends TestCase
{
    public function testProveArray(): void
    {
        $this->assertInstanceOf(None::class, proveArray(new FooIterable()));
        $this->assertInstanceOf(Some::class, proveArray([]));
    }

    public function testProveNonEmptyArray(): void
    {
        $this->assertInstanceOf(None::class, proveNonEmptyArray(new FooIterable()));
        $this->assertInstanceOf(None::class, proveNonEmptyArray([]));
        $this->assertInstanceOf(Some::class, proveNonEmptyArray([1]));
    }

    public function testProveArrayOf(): void
    {
        $this->assertInstanceOf(None::class, proveArrayOf(new FooIterable(), Foo::class, true));
        $this->assertInstanceOf(None::class, proveArrayOf(new FooIterable(), Foo::class, false));

        $this->assertInstanceOf(Some::class, proveArrayOf([], Foo::class, false));
        $this->assertInstanceOf(None::class, proveArrayOf([], Foo::class, true));

        $this->assertInstanceOf(Some::class, proveArrayOf([new Foo(1)], Foo::class, false));
        $this->assertInstanceOf(None::class, proveArrayOf([new Foo(1), new Bar(true)], Foo::class));
    }

    public function testProveNonEmptyArrayOf(): void
    {
        $this->assertInstanceOf(None::class, proveNonEmptyArrayOf(new FooIterable(), Foo::class));
        $this->assertInstanceOf(None::class, proveNonEmptyArrayOf([], Foo::class));
        $this->assertInstanceOf(None::class, proveNonEmptyArrayOf([1, new Foo(1)], Foo::class));
        $this->assertInstanceOf(Some::class, proveNonEmptyArrayOf([new Foo(1)], Foo::class));
    }
}
