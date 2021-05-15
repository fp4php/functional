<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class ReduceTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\reduce(
                getCollection(),
                fn(int $acc, int $v) => $acc + $v
            );
        ';

        $this->assertBlockType($phpBlock, Option::class . '<int>');
    }
}
