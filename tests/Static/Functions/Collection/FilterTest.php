<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Collection;

use Tests\PhpBlockTestCase;

final class FilterTest extends PhpBlockTestCase
{
    public function testWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\filter;

            /** 
             * @psalm-return array<string, int> 
             */
            function getCollection(): array { return []; }

            $result = filter(
                getCollection(),
                fn(int $v, string $k) => true
            );
        ';

        $this->assertBlockType($phpBlock, 'array<string, int>');
    }

    public function testReconciliationWithArray(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\filter;

            /** 
             * @psalm-return array<string, null|int> 
             */
            function getCollection(): array { return []; }

            $result = filter(
                getCollection(),
                fn(null|int $v) => null !== $v
            );
        ';

        $this->assertBlockType($phpBlock, 'array<string, int>');
    }

    public function testReconciliationWithoutPreservingKeys(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\filter;

            /** 
             * @psalm-return array<string, null|int> 
             */
            function getCollection(): array { return []; }

            $result = filter(
                getCollection(),
                fn(null|int $v) => null !== $v,
                preserveKeys: false,
            );
        ';

        $this->assertBlockType($phpBlock, 'list<int>');
    }

    public function testReconciliationWithShapes(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\filter;

            /** 
             * @psalm-type Shape = array{name?: string, postcode?: int|string} 
             * @psalm-return array<string, Shape> 
             */
            function getCollection(): array { return []; }

            $result = filter(
                getCollection(),
                fn(array $v) => 
                    array_key_exists("name", $v) &&
                    array_key_exists("postcode", $v) &&
                    is_int($v["postcode"])
            );
        ';

        $this->assertBlockType($phpBlock, 'array<string, array{name: string, postcode: int}>');
    }

    public function testReconciliationWithPsalmAssert(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use function Fp\Collection\filter;

            /** 
             * @psalm-return array<string, array> 
             */
            function getCollection(): array { return []; }

            /**
             * @psalm-type Shape = array{name: string, postcode: int}
             * @psalm-assert-if-true Shape $shape
             */
            function isValidShape(array $shape): bool
            {
                return array_key_exists("name", $shape) && 
                    array_key_exists("postcode", $shape) &&
                    is_int($shape["postcode"]);
            }

            $result = filter(
                getCollection(),
                fn(array $v) => isValidShape($v)
            );
        ';

        $this->assertBlockType($phpBlock, 'array<string, array{name: string, postcode: int}>');
    }
}
