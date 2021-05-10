<?php

declare(strict_types=1);

namespace Tests\Static\Collection;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class FirstTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\first(
                getCollection(),
                fn(int $v, string $k) => true
            );
        ';

        $this->assertBlockType($phpBlock, Option::class . '<int>');
    }
}
