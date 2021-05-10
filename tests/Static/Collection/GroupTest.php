<?php

declare(strict_types=1);

namespace Tests\Static\Collection;

use Tests\PhpBlockTestCase;

final class GroupTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\group(
                getCollection(),
                fn(int $v, string $k) => $k . "10"
            );
        ';

        $this->assertBlockType($phpBlock, 'array<array-key, array<string, int>>');
    }
}
