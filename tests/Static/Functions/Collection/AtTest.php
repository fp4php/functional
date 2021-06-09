<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class AtTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\at(
                getCollection(),
                "key"
            );
        ';

        $this->assertBlockTypes($phpBlock, 'Option<int>');
    }
}
