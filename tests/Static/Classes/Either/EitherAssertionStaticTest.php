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
    public function testIsRightWithIfTrueBranch(Either $either): Right
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
    public function testIsRightWithIfFalseBranch(Either $either): Left
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
    public function testIsRightWithTernaryTrueBranch(Either $either): Right
    {
        return $either->isRight()
            ? $either
            : throw new Error();
    }

    /**
     * @param Either<string, int> $either
     * @return Left<string>
     */
    public function testIsRightWithTernaryFalseBranch(Either $either): Left
    {
        return $either->isRight()
            ? throw new Error()
            : $either;
    }

    /**
     * @param Either<string, int> $either
     * @return Left<string>
     */
    public function testIsLeftWithTernaryTrueBranch(Either $either): Left
    {
        return $either->isLeft()
            ? $either
            : throw new Error();
    }

    /**
     * @param Either<string, int> $either
     * @return Right<int>
     */
    public function testIsLeftWithTernaryFalseBranch(Either $either): Right
    {
        return $either->isLeft()
            ? throw new Error()
            : $either;
    }
}
