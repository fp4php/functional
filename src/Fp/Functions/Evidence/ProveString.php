<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of string type
 *
 * @psalm-template T
 *
 * @psalm-param T $potential
 *
 * @psalm-return Option<string>
 */
function proveString(mixed $potential): Option
{
    return Option::of(is_string($potential) ? $potential : null);
}

/**
 * Prove that subject is of class-string type
 *
 * @psalm-return Option<class-string>
 */
function proveClassString(string $potential): Option
{
    return Option::of(class_exists($potential) ? $potential : null);
}

/**
 * Prove that subject is of non-empty-string type
 *
 * @psalm-return Option<non-empty-string>
 */
function proveNonEmptyString(mixed $subject): Option
{
    return is_string($subject) && $subject !== ''
        ? Option::some($subject)
        : Option::none();
}

/**
 * Prove that subject is of callable-string type
 *
 * @psalm-return Option<callable-string>
 */
function proveCallableString(string $subject): Option
{
    return Option::of(is_callable($subject) ? $subject : null);
}


