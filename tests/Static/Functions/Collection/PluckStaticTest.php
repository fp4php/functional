<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\Mock\Foo;

use function Fp\Collection\pluck;

final class PluckStaticTest
{
    /**
     * @return array<0|1, int>
     */
    public function testWithClass(): array
    {
        return pluck([new Foo(1), new Foo(2)], "a");
    }

    /**
     * @return array<0|1, 1|2>
     */
    public function testWithObjectLikeArray(): array
    {
        return pluck([["a" => 1], ["a" => 2]], "a");
    }
}
