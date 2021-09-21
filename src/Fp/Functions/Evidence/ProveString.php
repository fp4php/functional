<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that subject is of string type
 *
 * ```php
 * >>> proveString('');
 * => Some('')
 *
 * >>> proveString(1);
 * => None
 * ```
 *
 * @psalm-pure
 * @psalm-template T
 * @psalm-param T $potential
 * @psalm-return Option<string>
 */
function proveString(mixed $potential): Option
{
    return Option::fromNullable(is_string($potential) ? $potential : null);
}

/**
 * Prove that subject is of class-string type
 *
 * ```php
 * >>> proveClassString(Foo:class);
 * => Some(Foo::class)
 *
 * >>> proveClassString('');
 * => None
 * ```
 *
 * @psalm-pure
 * @psalm-return Option<class-string>
 */
function proveClassString(mixed $potential): Option
{
    return proveString($potential)->filter(fn($fqcn) => class_exists($fqcn) || interface_exists($fqcn));
}

/**
 * Prove that subject is of non-empty-string type
 *
 * ```php
 * >>> proveNonEmptyString('text');
 * => Some('text')
 *
 * >>> proveNonEmptyString('');
 * => None
 * ```
 *
 * @psalm-pure
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
 * ```php
 * >>> proveCallableString('array_map');
 * => Some('array_map')
 *
 * >>> proveCallableString('1');
 * => None
 * ```
 *
 * @psalm-pure
 * @psalm-return Option<callable-string>
 */
function proveCallableString(mixed $subject): Option
{
    return proveString($subject)->filter(fn($string) => is_callable($string));
}


