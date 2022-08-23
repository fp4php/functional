<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Bar;
use Tests\Mock\Foo;
use Tests\Mock\FooIterable;

use function Fp\Evidence\proveArray;
use function Fp\Evidence\proveArrayOf;
use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveNonEmptyArrayOf;
use function Fp\Evidence\proveString;

final class ProveArrayTest extends TestCase
{
    public function testProveArray(): void
    {
        $this->assertInstanceOf(None::class, proveArray(new FooIterable()));
        $this->assertInstanceOf(Some::class, proveArray([]));
    }

    public function testProveArrayOfValueType(): void
    {
        $this->assertEquals(Some::some([]), proveArray([], vType: proveInt(...)));
        $this->assertEquals(Some::some([1, 2]), proveArray([1, 2], vType: proveInt(...)));
        $this->assertEquals(Some::none(), proveArray(['1', '2'], vType: proveInt(...)));
    }

    public function testProveArrayOfKeyType(): void
    {
        $this->assertEquals(
            Some::some([]),
            proveArray([], kType: proveString(...)),
        );

        $this->assertEquals(
            Some::some(['fst' => 1, 'snd' => 2]),
            proveArray(['fst' => 1, 'snd' => 2], kType: proveString(...)),
        );

        $this->assertEquals(
            Some::none(),
            proveArray([0 => '1', 1 => '2'], kType: proveString(...)),
        );
    }

    public function testProveArrayOfType(): void
    {
        $this->assertEquals(
            Some::some([]),
            proveArray([], kType: proveString(...), vType: proveInt(...)),
        );

        $this->assertEquals(
            Some::some(['fst' => 1, 'snd' => 2]),
            proveArray(['fst' => 1, 'snd' => 2], kType: proveString(...), vType: proveInt(...)),
        );

        $this->assertEquals(
            Some::none(),
            proveArray(['fst' => '1', 'snd' => '2'], kType: proveString(...), vType: proveInt(...)),
        );
    }

    public function testProveNonEmptyArray(): void
    {
        $this->assertInstanceOf(None::class, proveNonEmptyArray(new FooIterable()));
        $this->assertInstanceOf(None::class, proveNonEmptyArray([]));
        $this->assertInstanceOf(Some::class, proveNonEmptyArray([1]));
    }

    public function testProveArrayOf(): void
    {
        $this->assertInstanceOf(None::class, proveArrayOf(new FooIterable(), Foo::class));
        $this->assertInstanceOf(Some::class, proveArrayOf([], Foo::class));
        $this->assertInstanceOf(Some::class, proveArrayOf([new Foo(1)], Foo::class));
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
