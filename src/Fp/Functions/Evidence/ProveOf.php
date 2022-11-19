<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Closure;
use Fp\Functional\Option\Option;

use function Fp\Collection\exists;

/**
 * Prove that subject is of given class
 *
 * ```php
 * >>> proveOf(new Foo(1), Foo::class);
 * => Some(Foo(1))
 *
 * >>> proveOf(new Bar(2), Foo::class);
 * => None
 * ```
 *
 * @template TV
 * @template TVO
 *
 * @param TV $subject
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn fully qualified class name
 * @return Option<TVO>
 */
function proveOf(mixed $subject, string|array $fqcn, bool $invariant = false): Option
{
    /** @var Option<TVO> */
    return proveObject($subject)->filter(fn(object $object) => exists(
        is_array($fqcn) ? $fqcn : [$fqcn],
        fn($fqcn) => $invariant ? $object::class === $fqcn : is_a($object, $fqcn),
    ));
}

/**
 * Curried version of {@see proveOf}.
 *
 * ```php
 * >>> proveList([1, 2, 3], of(Foo::class))
 * => Some(Foo(1))
 * ```
 *
 * @template TVO
 *
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn
 * @return Closure(mixed): Option<TVO>
 */
function of(string|array $fqcn, bool $invariant = false): Closure
{
    return fn(mixed $subject) => proveOf($subject, $fqcn, $invariant);
}
