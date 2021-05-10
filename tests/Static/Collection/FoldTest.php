<?php

declare(strict_types=1);

namespace Tests\Static\Collection;

use Tests\PhpBlockTestCase;

final class FoldTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\fold(
                0,
                getCollection(),
                fn(int $acc, int $v) => $acc + $v
            );
        ';

        $this->assertBlockType($phpBlock, 'int');
    }
}
