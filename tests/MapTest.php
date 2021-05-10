<?php

declare(strict_types=1);

namespace Tests;

final class MapTest extends PhpBlockTestCase
{
    public function testListOfInt(): void
    {
        $phpBlock = <<<'PHP'
            use function Fp\Collection\map;

            /** 
             * @psalm-return list<int> 
             */
            function getCollection() {}
            
            $result = map(getCollection(), fn(int $value, int $key) => (string) $value);
        PHP;

        $this->assertBlockType($phpBlock, 'array<int, numeric-string>');
    }

    public function testArrayOfInt(): void
    {
        $phpBlock = <<<'PHP'
            use function Fp\Collection\map;

            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection() {}
            
            $result = map(getCollection(), fn(int $value, int $key) => (string) $value);
        PHP;

        $this->assertBlockType($phpBlock, 'array<string, numeric-string>');
    }
}
