<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class MapTest extends PhpBlockTestCase
{
    public function testListOfInt(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            use function Fp\Collection\map;

            /** 
             * @psalm-return list<int> 
             */
            function getCollection() {}
            
            $result = map(getCollection(), fn(int $value, int $key) => (string) $value);
        ';

        $this->assertBlockTypes($phpBlock, 'list<numeric-string>');
    }

    public function testArrayOfInt(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            use function Fp\Collection\map;

            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection() {}
            
            $result = map(getCollection(), fn(int $value, int $key) => (string) $value);
        ';

        $this->assertBlockTypes($phpBlock, 'array<string, numeric-string>');
    }
}
