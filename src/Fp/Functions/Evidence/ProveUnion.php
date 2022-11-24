<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Closure;
use Fp\Functional\Option\Option;

use function Fp\Collection\firstMap;

/**
 * Prove that subject is one of given types
 *
 * ```php
 * >>> proveUnion(1, [proveString(...), proveInt(...)]);
 * => Some(1)
 * >>> proveUnion('str', [proveString(...), proveInt(...)]);
 * => Some('str')
 * >>> proveUnion(0.42, [proveString(...), proveInt(...)]);
 * => None
 * ```
 *
 * @template T
 *
 * @param non-empty-list<Closure(mixed): Option<T>> $evidences
 * @return Option<T>
 */
function proveUnion(mixed $subject, array $evidences): Option
{
    return firstMap($evidences, fn(Closure $prove) => $prove($subject));
}

/**
 * Curried version of {@see proveUnion()}
 *
 * ```php
 * >>> union([proveString(...), proveInt(...)])(1);
 * => Some(1)
 * >>> union([proveString(...), proveInt(...)])('str');
 * => Some('str')
 * >>> union([proveString(...), proveInt(...)])(0.42);
 * => None
 * ```
 *
 * @template T
 *
 * @param non-empty-list<Closure(mixed): Option<T>> $evidences
 * @return Closure(mixed): Option<T>
 */
function union(array $evidences): Closure
{
    return fn(mixed $subject) => proveUnion($subject, $evidences);
}

/**
 * Curried and varargs version of {@see proveUnion()}
 *
 * ```php
 * >>> unionT(proveString(...), proveInt(...))(1);
 * => Some(1)
 * >>> unionT(proveString(...), proveInt(...))('str');
 * => Some('str')
 * >>> unionT(proveString(...), proveInt(...))(0.42);
 * => None
 * ```
 *
 * @template T
 *
 * @param Closure(mixed): Option<T> $evidence
 * @param Closure(mixed): Option<T> ...$evidences
 * @return Closure(mixed): Option<T>
 */
function unionT(Closure $evidence, Closure ...$evidences): Closure
{
    return fn(mixed $subject) => proveUnion($subject, [$evidence, ...$evidences]);
}
