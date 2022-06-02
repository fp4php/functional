<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Validated;

use Error;
use Fp\Functional\Validated\Validated;

final class ValidatedAssertionStaticTest
{
    /**
     * @param Validated<string, int> $validated
     */
    public function testIsValidWithIfTrueBranch(Validated $validated): int
    {
        if ($validated->isValid()) {
            return $validated->get();
        } else {
            throw new Error();
        }
    }

    /**
     * @param Validated<string, int> $validated
     */
    public function testIsValidWithIfFalseBranch(Validated $validated): string
    {
        if ($validated->isValid()) {
            throw new Error();
        } else {
            return $validated->get();
        }
    }


    /**
     * @param Validated<string, int> $validated
     */
    public function testIsValidWithTernaryTrueBranch(Validated $validated): int
    {
        return $validated->isValid()
            ? $validated->get()
            : throw new Error();
    }

    /**
     * @param Validated<string, int> $validated
     */
    public function testIsValidWithTernaryFalseBranch(Validated $validated): string
    {
        return $validated->isValid()
            ? throw new Error()
            : $validated->get();
    }

    /**
     * @param Validated<string, int> $validated
     */
    public function testIsInvalidWithTernaryTrueBranch(Validated $validated): string
    {
        return $validated->isInvalid()
            ? $validated->get()
            : throw new Error();
    }

    /**
     * @param Validated<string, int> $validated
     */
    public function testIsInvalidWithTernaryFalseBranch(Validated $validated): int
    {
        return $validated->isInvalid()
            ? throw new Error()
            : $validated->get();
    }
}
