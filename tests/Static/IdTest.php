<?php

declare(strict_types=1);

namespace Tests\Static;

use Fp\Functional\Option\Option;
use Fp\Functional\Tuple\Tuple2;
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
