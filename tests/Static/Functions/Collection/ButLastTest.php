<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class ButLastTest extends PhpBlockTestCase
{
    public function testWithNonEmptyArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return non-empty-array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\butLast(
                getCollection(),
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<string, int>');
    }

    public function testWithNonEmptyList(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return non-empty-list<int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\butLast(
                getCollection(),
            );
        ';

        $this->assertBlockTypes($phpBlock, 'list<int>');
    }
}
