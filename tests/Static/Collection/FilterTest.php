<?php

declare(strict_types=1);

namespace Tests\Static\Collection;

use Tests\PhpBlockTestCase;

final class FilterTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = <<<'PHP'
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\filter(
                getCollection(),
                fn(int $v, string $k) => true
            );
        PHP;

        $this->assertBlockType($phpBlock, 'array<string, int>');
    }
}
