<?php

declare(strict_types=1);

namespace Tests\Static;

use Tests\PhpBlockTestCase;

final class IdTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\id(
                getCollection(),
            );
        ';

        $this->assertBlockType($phpBlock, 'array<string, int>');
    }
}
