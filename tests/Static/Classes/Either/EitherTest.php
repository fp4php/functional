<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Tests\PhpBlockTestCase;

final class EitherTest extends PhpBlockTestCase
{

    public function testCreation(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;
                
                $result = Either::right(1);
            ',
            strtr('Right<empty, 1>', ['Right' => Right::class])
        );

        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;
                
                $result = Either::left(1);
            ',
            strtr('Left<1, empty>', ['Left' => Left::class])
        );
    }

    public function testGet(): void
    {
        $this->assertBlockType(
            /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;
                
                /**
                 * @psalm-return Either<string, int>
                 */
                function getEither(): Either {
                    return rand(0, 1)
                        ? Either::right(1)
                        : Either::left("error!");
                }
                
                $result = getEither()->get();
            ',
            'int|string'
        );
    }

    public function testMap(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;
                                
                $result = Either::left(1)
                    ->map(fn(mixed $v) => (string) $v)
                    ->get();
            ',
            '1|string'
        );
    }

    public function testFlatMap(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;                

                $result = Either::right(1)
                    ->flatMap(fn(int $v) => Either::right((string) $v))
                    ->get();
            ',
            'numeric-string'
        );
    }

    public function testSwap(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;
                
                /**
                 * @psalm-return Either<string, int>
                 */
                function getEither(): Either {
                    return rand(0, 1)
                        ? Either::right(1)
                        : Either::left("error!");
                }

                $result = getEither()
                    ->flatMap(fn(int $v) => Either::right((bool) $v))
                    ->swap();
            ',
            strtr('Either<bool,string>', ['Either' => Either::class])
        );
    }

    public function testMapLeft(): void
    {
        $this->assertBlockType(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;
                
                /**
                 * @psalm-return Either<string, int>
                 */
                function getEither(): Either {
                    return rand(0, 1)
                        ? Either::right(1)
                        : Either::left("error!");
                }

                $result = getEither()
                    ->flatMap(fn(int $v) => Either::right((float) $v))
                    ->mapLeft(fn(string $e) => (bool) $e)
                    ->mapLeft(fn(bool $e) => (int) $e);
            ',
            strtr('Either<0|1,float>', ['Either' => Either::class])
        );
    }
}
