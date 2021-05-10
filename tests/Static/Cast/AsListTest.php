<?php

declare(strict_types=1);

namespace Tests\Static\Cast;

use Tests\PhpBlockTestCase;

final class AsListTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Cast\asList(getCollection());
        ';

        $this->assertBlockType($phpBlock, 'list<int>');
    }
}
