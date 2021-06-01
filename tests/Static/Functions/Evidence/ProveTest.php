<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Fp\Functional\Option\Option;
use Tests\Mock\Foo;
use Tests\PhpBlockTestCase;

final class ProveTest extends PhpBlockTestCase
{
    public function testProveOf(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                $result = \Fp\Evidence\proveOf(new \Tests\Mock\Foo(1), \Tests\Mock\Foo::class);
            ',
            strtr('Option<Foo>', [
            'Option' => Option::class,
            'Foo' => Foo::class,
            ])
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                $result = \Fp\Evidence\proveOf(new \Tests\Mock\Bar(true), \Tests\Mock\Foo::class);
            ',
            strtr('Option<Foo>', [
                'Option' => Option::class,
                'Foo' => Foo::class,
            ])
        );
    }
}
