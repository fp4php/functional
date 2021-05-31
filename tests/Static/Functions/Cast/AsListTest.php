<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Cast;

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
            
            $result = Fp\Cast\asList(getCollection());
        ';

        $this->assertBlockTypes($phpBlock, 'list<int>');
    }

    public function testWithNonEmptyArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return non-empty-array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = Fp\Cast\asList(getCollection());
        ';

        $this->assertBlockTypes($phpBlock, 'non-empty-list<int>');
    }

    public function testWithNonEmptyList(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return non-empty-list<int> 
             */
            function getCollection(): array { return []; }
            
            $result = Fp\Cast\asList(getCollection());
        ';

        $this->assertBlockTypes($phpBlock, 'non-empty-list<int>');
    }
}
