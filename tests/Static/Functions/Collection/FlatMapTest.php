<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class FlatMapTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\flatMap(
                getCollection(),
                fn(int $v, string $k) => [$v - 1, $v + 1]
            );
        ';

        $this->assertBlockType($phpBlock, 'list<int>');
    }
}
