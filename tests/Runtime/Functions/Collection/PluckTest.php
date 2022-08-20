<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Mock\Foo;

use function Fp\Collection\pluck;

final class PluckTest extends TestCase
{
    public function testPluck(): void
    {
        $this->assertEquals(
            [1, 2],
            pluck(
                [['a' => 1], ['a' => 2]],
                'a'
            )
        );

        $this->assertEquals(
            [1, 3],
            pluck(
                [new Foo(1), new Foo(3)],
                'a'
            )
        );
    }
}
