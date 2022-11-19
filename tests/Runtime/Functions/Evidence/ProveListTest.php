<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Evidence;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use PHPUnit\Framework\TestCase;
use Tests\Mock\FooIterable;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveList;
use function Fp\Evidence\proveNonEmptyList;

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

    public function testProveListOfType(): void
    {
        $this->assertEquals(Some::some([]), proveList([], proveInt(...)));
        $this->assertEquals(Some::some([1, 2]), proveList([1, 2], proveInt(...)));
        $this->assertEquals(Some::none(), proveList(['1', '2'], proveInt(...)));
    }

    public function testProveNonEmptyList(): void
    {
        $this->assertInstanceOf(None::class, proveNonEmptyList(new FooIterable()));
        $this->assertInstanceOf(None::class, proveNonEmptyList([]));
        $this->assertInstanceOf(Some::class, proveNonEmptyList([1]));
        $this->assertInstanceOf(None::class, proveNonEmptyList([1 => 1]));
    }
}
