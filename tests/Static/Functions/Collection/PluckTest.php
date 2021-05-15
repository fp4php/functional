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
            
            $result = pluck([new Foo(1), new Foo(2)], "a");
        ';

        $this->assertBlockType($phpBlock, 'array<array-key, int>');
    }
}
