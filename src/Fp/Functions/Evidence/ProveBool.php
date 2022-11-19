<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of boolean type
 *
 * ```php
 * >>> proveBool(true);
 * => Some(true)
 *
 * >>> proveBool(1);
 * => None
 * ```
 *
 * @return Option<bool>
 */
function proveBool(mixed $subject): Option
{
    return Option::fromNullable(is_bool($subject) ? $subject : null);
}

/**
 * Prove that subject is of boolean type
 * and it's value is true
 *
 * ```php
 * >>> proveTrue(true);
 * => Some(true)
 *
 * >>> proveTrue(1);
 * => None
 * ```
 *
 * @return Option<true>
 */
function proveTrue(mixed $subject): Option
{
    return Option::fromNullable(true === $subject ? $subject : null);
}

/**
 * Prove that subject is of boolean type
 * and it's value is false
 *
 * ```php
 * >>> proveFalse(false);
 * => Some(false)
 *
 * >>> proveFalse(true);
 * => None
 * ```
 *
 * @return Option<false>
 */
function proveFalse(mixed $subject): Option
{
    return Option::fromNullable(false === $subject ? $subject : null);
}
