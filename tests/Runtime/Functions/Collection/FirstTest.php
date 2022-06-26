<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\first;
use function Fp\Collection\firstOf;

final class FirstTest extends TestCase
{
    public function testFirst(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(1, first($c)->get());
        $this->assertEquals(2, first($c, fn(int $v) => $v === 2)->get());
    }

    public function testFirstInstanceOf(): void
    {
        $foo = new Foo(1);

        $this->assertEquals($foo, firstOf([1, $foo, 3], Foo::class)->get());
        $this->assertNull(firstOf([1, $foo, 3], Bar::class)->get());
    }
}
