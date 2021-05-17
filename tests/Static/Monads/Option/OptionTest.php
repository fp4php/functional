<?php

declare(strict_types=1);

namespace Tests\Static\Monads\Option;

use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class OptionTest extends PhpBlockTestCase
{
    public function testCreation(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                /** @var int|null $value */
                $value = null;
                
                $result = Option::of($value);
            ',
            strtr('Option<int>', ['Option' => Option::class])
        );
    }

    public function testGet(): void
    {
        $this->assertBlockType(
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
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                $result = Option::of(1)
                    ->map(fn(int $v) => (string) $v)
                    ->get();
            ',
            'null|numeric-string'
        );
    }

    public function testFlatMap(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;
                
                $result = Option::of(1)
                    ->flatMap(fn(int $v) => Option::of((string) $v))
                    ->get();
            ',
            'null|numeric-string'
        );
    }
}
