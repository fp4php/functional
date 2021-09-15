<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Error;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;

final class OptionAssertionStaticTest
{
    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsSomeWithIfTrueBranch(Option $option): Option
    {
        if ($option->isSome()) {
            return $option;
        } else {
            throw new Error();
        }
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsSomeWithIfFalseBranch(Option $option): Option
    {
        if ($option->isSome()) {
            throw new Error();
        } else {
            return $option;
        }
    }

    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsNonEmptyWithIfTrueBranch(Option $option): Some
    {
        if ($option->isNonEmpty()) {
            return $option;
        } else {
            throw new Error();
        }
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsNonEmptyWithIfFalseBranch(Option $option): None
    {
        if ($option->isNonEmpty()) {
            throw new Error();
        } else {
            return $option;
        }
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsNoneWithTrueBranch(Option $option): None
    {
        if ($option->isNone()) {
            return $option;
        } else {
            throw new Error();
        }
    }

    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsNoneWithFalseBranch(Option $option): Some
    {
        if ($option->isNone()) {
            throw new Error();
        } else {
            return $option;
        }
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsEmptyWithTrueBranch(Option $option): None
    {
        if ($option->isEmpty()) {
            return $option;
        } else {
            throw new Error();
        }
    }

    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsNonEmptyWithTernaryTrueBranch(Option $option): Some
    {
        return $option->isNonEmpty()
            ? call_user_func(function() use ($option) {
                return $option;
            })
            : throw new Error();
    }

    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsEmptyWithFalseBranch(Option $option): Some
    {
        if ($option->isEmpty()) {
            throw new Error();
        } else {
            return $option;
        }
    }

    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsSomeWithTernaryTrueBranch(Option $option): Some
    {
        return $option->isSome()
            ? call_user_func(function() use ($option) {
                return $option;
            })
            : throw new Error();
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsSomeWithTernaryFalseBranch(Option $option): None
    {
        return $option->isSome()
            ? throw new Error()
            : call_user_func(function() use ($option) {
                return $option;
            });
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsNonEmptyWithTernaryFalseBranch(Option $option): None
    {
        return $option->isNonEmpty()
            ? throw new Error()
            : call_user_func(function() use ($option) {
                return $option;
            });
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
     * @return Some<int>
     */
    public function testIsNoneWithTernaryFalseBranch(Option $option): Some
    {
        return $option->isNone()
            ? throw new Error()
            : call_user_func(function() use ($option) {
                return $option;
            });
    }

    /**
     * @param Option<int> $option
     * @return None
     */
    public function testIsEmptyWithTernaryTrueBranch(Option $option): None
    {
        return $option->isEmpty()
            ? call_user_func(function() use ($option) {
                return $option;
            })
            : throw new Error();
    }

    /**
     * @param Option<int> $option
     * @return Some<int>
     */
    public function testIsEmptyWithTernaryFalseBranch(Option $option): Some
    {
        return $option->isEmpty()
            ? throw new Error()
            : call_user_func(function() use ($option) {
                return $option;
            });
    }
}
