<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Tests\Mock\Foo;
use PHPUnit\Framework\TestCase;

use function Fp\Collection\unique;
use function Fp\Collection\uniqueBy;

final class UniqueTest extends TestCase
{
    public function testListUnique(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            unique([1, 1, 2, 2, 3, 3]),
        );
    }

    public function testArrayUnique(): void
    {
        $this->assertEquals(
            ['a' => 1, 'c' => 2, 'e' => 3],
            unique(['a' => 1, 'b' => 1, 'c' => 2, 'd' => 2, 'e' => 3, 'f' => 3]),
        );
    }

    public function testListUniqueBy(): void
    {
        $items = [
            new Foo(a: 1, b: true),
            new Foo(a: 1, b: false),
            new Foo(a: 2, b: true),
            new Foo(a: 2, b: false),
        ];

        $this->assertEquals(
            [new Foo(a: 1, b: true), new Foo(a: 2, b: true)],
            uniqueBy($items, fn(Foo $value) => $value->a),
        );
    }

    public function testArrayUniqueBy(): void
    {
        $items = [
            'a' => new Foo(a: 1, b: true),
            'b' => new Foo(a: 1, b: false),
            'c' => new Foo(a: 2, b: true),
            'd' => new Foo(a: 2, b: false),
        ];

        $this->assertEquals(
            ['a' => new Foo(a: 1, b: true), 'c' => new Foo(a: 2, b: true)],
            uniqueBy($items, fn(Foo $value) => $value->a),
        );
    }
}
