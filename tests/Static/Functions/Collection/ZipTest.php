<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class ZipTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollectionA(): array { return []; }
            
            /** 
             * @psalm-return iterable<string, bool> 
             */
            function getCollectionB(): array { return []; }
            
            $result = \Fp\Collection\zip(
                getCollectionA(),
                getCollectionB(), 
            );
        ';

        $this->assertBlockType($phpBlock, 'list<array{int, bool}>');
    }
}
