<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class OptionFilterTest extends PhpBlockTestCase
{
    public function testRefineNotNull(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use Fp\Functional\Option\Option;

            /** 
             * @psalm-return Option<null|int> 
             */
            function getOption(): array { return []; }

            $result = getOption()->filter(fn(null|int $v) => null !== $v);
        ';

        $this->assertBlockTypes($phpBlock, 'Option<int>');
    }

    public function testRefineShapeType(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use Fp\Functional\Option\Option;

            /** 
             * @psalm-type Shape = array{name?: string, postcode?: int|string} 
             * @psalm-return Option<Shape> 
             */
            function getOption(): array { return []; }

            $result = getOption()->filter(
                fn(array $v) => 
                    array_key_exists("name", $v) &&
                    array_key_exists("postcode", $v) &&
                    is_int($v["postcode"])
            );
        ';

        $this->assertBlockTypes($phpBlock, 'Option<array{name: string, postcode: int}>');
    }

    public function testRefineShapeWithPsalmAssert(): void
    {
        $phpBlock = /** @lang InjectablePHP */
            '
            use Fp\Functional\Option\Option;

            /** 
             * @psalm-return Option<array> 
             */
            function getOption(): array { return []; }

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

            $result = getOption()->filter(
                fn(array $v) => isValidShape($v)
            );
        ';

        $this->assertBlockTypes($phpBlock, 'Option<array{name: string, postcode: int}>');
    }
}
