<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Error;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;

final class OptionAssertionStaticTest
{
    /**
     * @param Option<int> $option
     */
    public function testIsSomeWithIfTrueBranch(Option $option): int
    {
        if ($option->isSome()) {
            return $option->get();
        } else {
            throw new Error();
        }
    }

    /**
     * @param Option<int> $option
     */
    public function testIsSomeWithIfFalseBranch(Option $option): int|null
    {
        if ($option->isSome()) {
            throw new Error();
        } else {
            return $option->get();
        }
    }

    /**
     * @param Option<int> $option
     */
    public function testIsNoneWithTrueBranch(Option $option): int|null
    {
        if ($option->isNone()) {
            return $option->get();
        } else {
            throw new Error();
        }
    }

    /**
     * @param Option<int> $option
     */
    public function testIsNoneWithFalseBranch(Option $option): int
    {
        if ($option->isNone()) {
            throw new Error();
        } else {
            return $option->get();
        }
    }

    /**
     * @param Option<int> $option
     */
    public function testIsSomeWithTernaryTrueBranch(Option $option): int
    {
        return $option->isSome()
            ? $option->get()
            : throw new Error();
    }

    /**
     * @param Option<int> $option
     */
    public function testIsSomeWithTernaryFalseBranch(Option $option): int|null
    {
        return $option->isSome()
            ? throw new Error()
            : $option->get();
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsNoneWithTernaryTrueBranch(Option $option): None
    {
        return $option->isNone()
            ? call_user_func(function() use ($option) {
                return $option;
            })
            : throw new Error();
    }

    /**
     * @param Option<int> $option
     */
    public function testIsNoneWithTernaryFalseBranch(Option $option): int
    {
        return $option->isNone()
            ? throw new Error()
            : $option->get();
    }
}
