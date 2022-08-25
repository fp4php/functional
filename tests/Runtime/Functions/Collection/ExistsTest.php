<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Bar;
use Tests\Mock\Baz;
use Tests\Mock\Foo;

use function Fp\Collection\exists;
use function Fp\Collection\existsKV;
use function Fp\Collection\existsOf;

final class ExistsTest extends TestCase
{
    public function testExists(): void
    {
        /** @var list<int> $c */
        $c = [1, 2, 3];

        $this->assertTrue(exists($c, fn (int $v) => $v === 3));
        $this->assertFalse(exists($c, fn (int $v) => $v === 4));
    }

    public function testExistsKV(): void
    {
        /** @var array<string, int> $c */
        $c = [
            'fst' => 1,
            'snd' => 2,
            'thr' => 3,
        ];

        $this->assertTrue(existsKV($c, fn($k, $v) => $k === 'snd' && $v === 2));
        $this->assertFalse(existsKV($c, fn($k, $v) => $k === 'fth' && $v === 4));
    }

    public function testAnyOf(): void
    {
        $this->assertTrue(existsOf(
            [1, new Foo(1)],
            Foo::class
        ));

        $this->assertFalse(existsOf(
            [1, new Foo(1)],
            Bar::class
        ));

        $this->assertFalse(existsOf(
            [1, new Foo(1)],
            [Bar::class, Baz::class],
        ));

        $this->assertTrue(existsOf(
            [1, new Foo(1)],
            [Bar::class, Foo::class],
        ));

        $this->assertTrue(existsOf(
            [1, new Foo(1)],
            [Foo::class, Bar::class],
        ));
    }
}
