<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class PluckTest extends PhpBlockTestCase
{
    public function testIntProperty(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            use Tests\Mock\Foo;
            use function Fp\Collection\pluck;
            
            /** @psalm-trace $res1 */
            $res1 = pluck([new Foo(1), new Foo(2)], "a");
            
            /** @psalm-trace $res2 */
            $res2 = pluck([["a" => 1], ["a" => 2]], "a");
        ';

        $this->assertBlockTypes(
            $phpBlock,
            'array<0|1, int>',
            'array<0|1, 1|2>',
        );
    }
}
