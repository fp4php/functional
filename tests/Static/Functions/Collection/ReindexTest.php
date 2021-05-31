<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class ReindexTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\reindex(
                getCollection(),
                fn(int $v, string $k) => $v 
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<int, int>');
    }
}
