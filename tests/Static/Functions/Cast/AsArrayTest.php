<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Cast;

use Tests\PhpBlockTestCase;

final class AsArrayTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Cast\asArray(getCollection());
        ';

        $this->assertBlockTypes($phpBlock, 'array<string, int>');
    }
}
