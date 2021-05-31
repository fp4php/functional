<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class ReverseTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\reverse(
                getCollection(),
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<string, int>');
    }
}
