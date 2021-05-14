<?php

declare(strict_types=1);

namespace Tests\Runtime\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\at;
use function Fp\Collection\copyCollection;
use function Fp\Collection\every;
use function Fp\Collection\filter;
use function Fp\Collection\first;
use function Fp\Collection\firstInstanceOf;
use function Fp\Collection\flatMap;
use function Fp\Collection\fold;
use function Fp\Collection\group;
use function Fp\Collection\head;
use function Fp\Collection\last;
use function Fp\Collection\map;
use function Fp\Collection\partition;
use function Fp\Collection\pluck;
use function Fp\Collection\pop;
use function Fp\Collection\reduce;
use function Fp\Collection\reverse;
use function Fp\Collection\second;
use function Fp\Collection\shift;
use function Fp\Collection\some;
use function Fp\Collection\tail;

final class FirstTest extends TestCase
{
    public function testFirst(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(1, first($c)->get());
        $this->assertEquals(
            2,
            first($c, fn(int $v, int $k) => $k === 1)->get()
        );
    }

    public function testFirstInstanceOf(): void
    {
        $foo = new Foo(1);

        $this->assertEquals($foo, firstInstanceOf([1, $foo, 3], Foo::class)->get());
        $this->assertNull(firstInstanceOf([1, $foo, 3], Bar::class)->get());
    }
}
