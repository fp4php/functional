<?php

declare(strict_types=1);

namespace Tests\Static\Cast;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class AsNonEmptyListTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Cast\asNonEmptyList(getCollection());
        ';

        $this->assertBlockType($phpBlock, Option::class . '<non-empty-list<int>>');
    }
}
