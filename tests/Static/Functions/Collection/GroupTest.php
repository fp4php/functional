<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

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

            $result = \Fp\Collection\groupBy(
                getCollection(),
                fn(int $v, string $k) => /** @var array-key */ $k . "10"
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<non-empty-string, array<string, int>>');
    }

    public function testWithNonEmptyArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return non-empty-array<string, int> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\groupBy(
                getCollection(),
                fn(int $v, string $k) => /** @var array-key */ $k . "10"
            );
        ';

        $this->assertBlockTypes($phpBlock, 'non-empty-array<non-empty-string, non-empty-array<string, int>>');
    }

    public function testWithNonEmptyList(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return non-empty-list<int> 
             */
            function getCollection(): array { return [1]; }

            $result = \Fp\Collection\groupBy(
                getCollection(),
                fn(int $v, string $k) => /** @var array-key */ $k . "10"
            );
        ';

        $this->assertBlockTypes($phpBlock, 'non-empty-array<non-empty-string, non-empty-array<int, int>>');
    }

    public function testWithListInferGroupKey(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return list<string> 
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\groupBy(
                getCollection(),
                fn(string $value) => $value . "10"
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<non-empty-string, array<int, string>>');
    }

    public function testWithArrayInferGroupKey(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-return array<non-empty-string, string>
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\groupBy(
                getCollection(),
                fn(string $value) => $value . "10"
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<non-empty-string, array<non-empty-string, string>>');
    }

    public function testWithArrayAndGroupKeyAsTypeAlias(): void
    {
        $phpBlock = /** @lang InjectablePHP */ '
            /** 
             * @psalm-type Alias = string
             * @psalm-return array<Alias, int>
             */
            function getCollection(): array { return []; }
            
            $result = \Fp\Collection\groupBy(
                getCollection(),
                fn(string $value) => $value
            );
        ';

        $this->assertBlockTypes($phpBlock, 'array<string, array<string, int>>');
    }
}
