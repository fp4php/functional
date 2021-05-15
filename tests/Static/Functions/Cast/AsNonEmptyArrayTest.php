<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Cast;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class AsNonEmptyArrayTest extends PhpBlockTestCase
{
    public function testWithIterable(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return iterable<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Cast\asNonEmptyArray(getCollection());
        ';

        $this->assertBlockType($phpBlock, Option::class . '<non-empty-array<string, int>>');
    }
}
