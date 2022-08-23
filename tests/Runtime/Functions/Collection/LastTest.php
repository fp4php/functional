<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Collection;

use Fp\Functional\Option\Option;
use PHPUnit\Framework\TestCase;

use Tests\Mock\Foo;
use function Fp\Collection\last;
use function Fp\Collection\lastOf;

final class LastTest extends TestCase
{
    public function testLast(): void
    {
        $c = [1, 2, 3];

        $this->assertEquals(
            3,
            last($c)->get()
        );
    }
    public function testLastOf(): void
    {
        $c = [1, new Foo(1), new Foo(2)];
        $this->assertEquals(Option::some(new Foo(2)), lastOf($c, Foo::class));
    }
}
