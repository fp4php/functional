<?php

declare(strict_types=1);

namespace Fp;

use function Fp\Collection\exists;
use function Fp\Evidence\proveClassString;
use function Fp\Evidence\proveObject;

/**
 * Check if object is of given class
 *
 * ```php
 * >>> of(new Foo(1), Foo::class);
 * => true
 * ```
 *
 * @template TO
 *
 * @param class-string<TO>|list<class-string<TO>> $fqcn
 * @param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-assert-if-true TO $subject
 */
function of(mixed $subject, string|array $fqcn, bool $invariant = false): bool
{
    return objectOf($subject, $fqcn, $invariant);
}

/**
 * Check if object is of given class
 *
 * ```php
 * >>> of(new Foo(1), Foo::class);
 * => true
 * ```
 *
 * @template TO
 *
 * @param class-string<TO>|list<class-string<TO>> $fqcn
 * @param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-assert-if-true TO $subject
 */
function objectOf(mixed $subject, string|array $fqcn, bool $invariant = false): bool
{
    $fqcnList = is_string($fqcn) ? [$fqcn] : $fqcn;

    return proveObject($subject)
        ->map(fn(object $object) => exists(
            $fqcnList,
            fn($fqcn) => $invariant
                ? $object::class === $fqcn
                : is_a($object, $fqcn)
        ))
        ->getOrElse(false);
}

/**
 * Check if string is of given class
 *
 * ```php
 * >>> of(Foo::class, Foo::class);
 * => true
 * ```
 *
 * @template TO
 *
 * @param class-string<TO>|list<class-string<TO>> $fqcn
 * @param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-assert-if-true class-string<TO> $subject
 */
function classOf(mixed $subject, string|array $fqcn, bool $invariant = false): bool
{
    $fqcnList = is_string($fqcn) ? [$fqcn] : $fqcn;

    return proveClassString($subject)
        ->map(fn(string $classString) => exists(
            $fqcnList,
            fn($fqcn) => $invariant
                ? $classString === $fqcn
                : is_a($classString, $fqcn, true),
        ))
        ->getOrElse(false);
}
