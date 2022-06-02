<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Error;
use Fp\Functional\Either\Either;

final class EitherAssertionStaticTest
{
    /**
     * @param Either<string, int> $either
     */
    public function testIsRightWithIfTrueBranch(Either $either): int
    {
        if ($either->isRight()) {
            return $either->get();
        } else {
            throw new Error();
        }
    }

    /**
     * @param Either<string, int> $either
     */
    public function testIsRightWithIfFalseBranch(Either $either): string
    {
        if ($either->isRight()) {
            throw new Error();
        } else {
            return $either->get();
        }
    }

    /**
     * @param Either<string, int> $either
     */
    public function testIsRightWithTernaryTrueBranch(Either $either): int
    {
        return $either->isRight()
            ? $either->get()
            : throw new Error();
    }

    /**
     * @param Either<string, int> $either
     * @return string
     */
    public function testIsRightWithTernaryFalseBranch(Either $either): string
    {
        return $either->isRight()
            ? throw new Error()
            : $either->get();
    }

    /**
     * @param Either<string, int> $either
     */
    public function testIsLeftWithTernaryTrueBranch(Either $either): string
    {
        return $either->isLeft()
            ? $either->get()
            : throw new Error();
    }

    /**
     * @param Either<string, int> $either
     */
    public function testIsLeftWithTernaryFalseBranch(Either $either): int
    {
        return $either->isLeft()
            ? throw new Error()
            : $either->get();
    }
}
