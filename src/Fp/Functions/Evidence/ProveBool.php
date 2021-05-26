<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of boolean type
 *
 * REPL:
 * >>> proveBool(true);
 * => Some<true>
 *
 *
 * @psalm-template T
 *
 * @psalm-param T $subject
 *
 * @psalm-return Option<bool>
 */
function proveBool(mixed $subject): Option
{
    return Option::fromNullable(is_bool($subject) ? $subject : null);
}

/**
 * Prove that subject is of boolean type
 * and it's value is true
 *
 * REPL:
 * >>> proveTrue(1);
 * => None
 *
 *
 * @psalm-template T
 *
 * @psalm-param T $subject
 *
 * @psalm-return Option<true>
 */
function proveTrue(mixed $subject): Option
{
    return Option::fromNullable(is_bool($subject) && true === $subject ? $subject : null);
}

/**
 * Prove that subject is of boolean type
 * and it's value is false
 *
 * REPL:
 * >>> proveFalse(false);
 * => Some<false>
 *
 *
 * @psalm-template T
 *
 * @psalm-param T $subject
 *
 * @psalm-return Option<false>
 */
function proveFalse(mixed $subject): Option
{
    return Option::fromNullable(is_bool($subject) && false === $subject ? $subject : null);
}
