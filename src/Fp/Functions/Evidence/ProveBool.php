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
 * @psalm-template T
 * @psalm-param T $subject
 * @psalm-return Option<bool>
 * @psalm-pure
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
 * @psalm-template T
 * @psalm-param T $subject
 * @psalm-return Option<true>
 * @psalm-pure
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
 * @psalm-template T
 * @psalm-param T $subject
 * @psalm-return Option<false>
 * @psalm-pure
 */
function proveFalse(mixed $subject): Option
{
    return Option::fromNullable(false === $subject ? $subject : null);
}
