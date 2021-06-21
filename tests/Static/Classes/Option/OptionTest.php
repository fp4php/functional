<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class OptionTest extends PhpBlockTestCase
{
    public function testCreation(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                /** @var int|null $value */
                $value = null;
                
                $result = Option::fromNullable($value);
            ',
            'Option<int>'
        );
    }

    public function testGet(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                /**
                 * @psalm-return Option<int>
                 */
                function getOption(): Option {
                    return rand(0, 1)
                        ? Option::some(1)
                        : Option::none();
                }
                
                $result = getOption()->get();
            ',
            'int|null'
        );
    }

    public function testMap(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                $result = Option::fromNullable(1)
                    ->map(fn(int $v) => (string) $v)
                    ->get();
            ',
            '"1"|null'
        );
    }

    public function testFlatMap(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                $result = Option::fromNullable(1)
                    ->flatMap(fn(int $v) => Option::fromNullable((string) $v))
                    ->get();
            ',
            '"1"|null'
        );
    }
}
