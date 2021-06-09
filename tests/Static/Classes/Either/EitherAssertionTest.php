<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Tests\PhpBlockTestCase;

final class EitherAssertionTest extends PhpBlockTestCase
{
    public function testIsRightWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;

                /** @var Either<string, int> */
                $either = Either::left("err");

                if ($either->isRight()) {
                    /** @psalm-trace $right */
                    $right = $either;
                } else {
                    /** @psalm-trace $left */
                    $left = $either;
                }
            ',
            'Right<int>',
            'Left<string>',
        );
    }

    public function testIsLeftWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;

                /** @var Either<string, int> */
                $either = Either::left("err");

                if ($either->isLeft()) {
                    /** @psalm-trace $left */
                    $left = $either;
                } else {
                    /** @psalm-trace $right */
                    $right = $either;
                }
            ',
            'Left<string>',
            'Right<int>',
        );
    }

    public function testIsRightWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;

                /** @var Either<string, int> */
                $either = Either::left("err");

                $either->isRight()
                    ? call_user_func(function() use ($either) {
                        /** @psalm-trace $right */
                        $right = $either;
                    })
                    : call_user_func(function() use ($either) {
                        /** @psalm-trace $left */
                        $left = $either;
                    });
            ',
            'Right<int>',
            'Left<string>',
        );
    }

    public function testIsLeftWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Either\Either;

                /** @var Either<string, int> */
                $either = Either::left("err");

                $either->isLeft()
                    ? call_user_func(function() use ($either) {
                        /** @psalm-trace $left */
                        $left = $either;
                    })
                    : call_user_func(function() use ($either) {
                        /** @psalm-trace $right */
                        $right = $either;
                    });
            ',
            'Left<string>',
            'Right<int>',
        );
    }
}
