<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Collection\first;
use function Fp\Collection\firstKV;
use function Fp\Collection\firstOf;

final class FirstTest extends TestCase
{
    public function testFirst(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(1, first($c)->get());
        $this->assertEquals(2, first($c, fn(int $v) => $v === 2)->get());
    }

    public function testFirstNull(): void
    {
        $c = [null, 2, 3];
        $this->assertEquals(Option::some(null), first($c));
    }

    public function testFirstKV(): void
    {
        /** @var array<string, int> $c */
        $c = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertEquals(Option::some(2), firstKV($c, fn($k, $v) => $k === 'snd' && $v === 2));
    }

    public function testFirstInstanceOf(): void
    {
        $foo = new Foo(1);

        $this->assertEquals($foo, firstOf([1, $foo, 3], Foo::class)->get());
        $this->assertNull(firstOf([1, $foo, 3], Bar::class)->get());
    }
}
