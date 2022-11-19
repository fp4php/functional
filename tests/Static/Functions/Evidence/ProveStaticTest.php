<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Fp\Functional\Option\Option;
use Tests\Mock\Bar;
use Tests\Mock\Foo;

use function Fp\Evidence\proveOf;

final class ProveStaticTest
{
    /**
     * @return Option<Foo>
     */
    public function testProveOf(): Option
    {
        return proveOf(new Foo(1), Foo::class);
    }

    /**
     * @return Option<Foo|Bar>
     */
    public function testProveOfWithMultipleClasses(): Option
    {
        return proveOf(new Foo(1), [Foo::class, Bar::class]);
    }
}
