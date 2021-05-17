<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\FooIterable;

use function Fp\Evidence\proveList;
use function Fp\Evidence\proveListOf;
use function Fp\Evidence\proveNonEmptyList;
use function Fp\Evidence\proveNonEmptyListOf;

final class ProveListTest extends TestCase
{
    public function testProveList(): void
    {
        $this->assertInstanceOf(None::class, proveList(new FooIterable()));
        $this->assertInstanceOf(Some::class, proveList([]));
        $this->assertInstanceOf(Some::class, proveList([1]));
        $this->assertInstanceOf(Some::class, proveList([0 => 1]));
        $this->assertInstanceOf(None::class, proveList([1 => 1]));
        $this->assertInstanceOf(Some::class, proveList(['0' => 1, '1' => 2, '2' => 3]));
        $this->assertInstanceOf(None::class, proveList(['0' => 1, '2' => 2, '3' => 3]));
    }

    public function testProveNonEmptyList(): void
    {
        $this->assertInstanceOf(None::class, proveNonEmptyList(new FooIterable()));
        $this->assertInstanceOf(None::class, proveNonEmptyList([]));
        $this->assertInstanceOf(Some::class, proveNonEmptyList([1]));
        $this->assertInstanceOf(None::class, proveNonEmptyList([1 => 1]));
    }

    public function testProveListOf(): void
    {
        $this->assertInstanceOf(None::class, proveListOf(new FooIterable(), Foo::class));
        $this->assertInstanceOf(Some::class, proveListOf([], Foo::class));
        $this->assertInstanceOf(None::class, proveListOf([1 => new Foo(1)], Foo::class));
        $this->assertInstanceOf(Some::class, proveListOf([new Foo(1)], Foo::class));
        $this->assertInstanceOf(None::class, proveListOf([new Foo(1), new Bar(true)], Foo::class));
    }

    public function testProveNonEmptyListOf(): void
    {
        $this->assertInstanceOf(None::class, proveNonEmptyListOf(new FooIterable(), Foo::class));
        $this->assertInstanceOf(None::class, proveNonEmptyListOf([], Foo::class));
        $this->assertInstanceOf(None::class, proveNonEmptyListOf([1, new Foo(1)], Foo::class));
        $this->assertInstanceOf(None::class, proveNonEmptyListOf([1 => new Foo(1)], Foo::class));
        $this->assertInstanceOf(Some::class, proveNonEmptyListOf([new Foo(1)], Foo::class));
    }
}
