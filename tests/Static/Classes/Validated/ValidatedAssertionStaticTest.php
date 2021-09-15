<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Validated;

use Error;
use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;

final class ValidatedAssertionStaticTest
{
    /**
     * @param Validated<string, int> $validated
     * @return Valid<int>
     */
    public function testIsValidWithIfTrueBranch(Validated $validated): Valid
    {
        if ($validated->isValid()) {
            return $validated;
        } else {
            throw new Error();
        }
    }

    /**
     * @param Validated<string, int> $validated
     * @return Invalid<string>
     */
    public function testIsValidWithIfFalseBranch(Validated $validated): Invalid
    {
        if ($validated->isValid()) {
            throw new Error();
        } else {
            return $validated;
        }
    }


    /**
     * @param Validated<string, int> $validated
     * @return Valid<int>
     */
    public function testIsValidWithTernaryTrueBranch(Validated $validated): Valid
    {
        return $validated->isValid()
            ? call_user_func(function() use ($validated) {
                return $validated;
            })
            : throw new Error();
    }

    /**
     * @param Validated<string, int> $validated
     * @return Invalid<string>
     */
    public function testIsValidWithTernaryFalseBranch(Validated $validated): Invalid
    {
        return $validated->isValid()
            ? throw new Error()
            : call_user_func(function() use ($validated) {
                return $validated;
            });
    }

    /**
     * @param Validated<string, int> $validated
     * @return Invalid<string>
     */
    public function testIsInvalidWithTernaryTrueBranch(Validated $validated): Invalid
    {
        return $validated->isInvalid()
            ? call_user_func(function() use ($validated) {
                return $validated;
            })
            : throw new Error();
    }

    /**
     * @param Validated<string, int> $validated
     * @return Valid<int>
     */
    public function testIsInvalidWithTernaryFalseBranch(Validated $validated): Valid
    {
        return $validated->isInvalid()
            ? throw new Error()
            : call_user_func(function() use ($validated) {
                return $validated;
            });
    }
}
