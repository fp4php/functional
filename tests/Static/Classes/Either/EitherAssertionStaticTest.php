<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Error;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;

final class EitherAssertionStaticTest
{
    /**
     * @param Either<string, int> $either
     * @return Right<int>
     */
    public function testIsRightWithIfTrueBranch(mixed $either): mixed
    {
        if ($either->isRight()) {
            return $either;
        } else {
            throw new Error();
        }
    }

    /**
     * @param Either<string, int> $either
     * @return Left<string>
     */
    public function testIsRightWithIfFalseBranch(mixed $either): mixed
    {
        if ($either->isRight()) {
            throw new Error();
        } else {
            return $either;
        }
    }

    /**
     * @param Either<string, int> $either
     * @return Right<int>
     */
    public function testIsRightWithTernaryTrueBranch(mixed $either): mixed
    {
        return $either->isRight()
            ? $either
            : throw new Error();
    }

    /**
     * @param Either<string, int> $either
     * @return Left<string>
     */
    public function testIsRightWithTernaryFalseBranch(mixed $either): mixed
    {
        return $either->isRight()
            ? throw new Error()
            : $either;
    }

    /**
     * @param Either<string, int> $either
     * @return Left<string>
     */
    public function testIsLeftWithTernaryTrueBranch(mixed $either): mixed
    {
        return $either->isLeft()
            ? $either
            : throw new Error();
    }

    /**
     * @param Either<string, int> $either
     * @return Right<int>
     */
    public function testIsLeftWithTernaryFalseBranch(mixed $either): mixed
    {
        return $either->isLeft()
            ? throw new Error()
            : $either;
    }
}
