<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Closure;
use Fp\Functional\Option\Option;

use function Fp\Collection\exists;

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
 * @return Option<string>
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
 * @return Option<class-string>
 */
function proveClassString(mixed $potential): Option
{
    return proveString($potential)->filter(fn($fqcn) => class_exists($fqcn) || interface_exists($fqcn));
}

/**
 * Prove that subject is of class-string<TVO> type
 *
 * ```php
 * >>> proveClassStringOf(ArrayList::class, Collection::class)
 * => Some(ArrayList::class)
 * >>> proveClassStringOf(Option::class, Collection::class)
 * => None
 *
 * ```
 *
 * @template TVO
 *
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn
 * @return Option<class-string<TVO>>
 */
function proveClassStringOf(mixed $potential, string|array $fqcn, bool $invariant = false): Option
{
    /** @var Option<class-string<TVO>> */
    return proveClassString($potential)->filter(fn(string $class) => exists(
        is_array($fqcn) ? $fqcn : [$fqcn],
        fn($fqcn) => $invariant ? $class === $fqcn : is_a($class, $fqcn, allow_string: true),
    ));
}

/**
 * Curried version of {@see proveClassStringOf}.
 *
 * ```php
 * >>> classStringOf(Collection::class)(ArrayList::class)
 * => Some(ArrayList::class)
 * >>> classStringOf(Collection::class)(Option::class)
 * => None
 *
 * ```
 *
 * @template TVO
 *
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn
 * @return Closure(mixed): Option<class-string<TVO>>
 */
function classStringOf(string|array $fqcn, bool $invariant = false): Closure
{
    return function(mixed $potential) use ($fqcn, $invariant) {
        /** @var Option<class-string<TVO>> */
        return proveClassStringOf($potential, $fqcn, $invariant);
    };
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
 * @return Option<non-empty-string>
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
 * @return Option<callable-string>
 */
function proveCallableString(mixed $subject): Option
{
    return proveString($subject)->filter(fn($string) => is_callable($string));
}


