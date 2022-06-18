<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\ExistsOfOperation;
use Fp\Operations\ExistsOperation;

/**
 * Find if there is element which satisfies the condition
 * false otherwise
 *
 * ```php
 * >>> exists([1, 2], fn(int $v): bool => $v === 1);
 * => true
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV, TK): bool $predicate
 */
function exists(iterable $collection, callable $predicate): bool
{
    return ExistsOperation::of($collection)($predicate);
}

/**
 * Returns true if there is collection element of given class
 * False otherwise
 *
 * ```php
 * >>> existsOf([new Foo(), 2, 3], Foo::class);
 * => true
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param class-string<TVO> $fqcn fully qualified class name
 * @param bool $invariant if turned on then subclasses are not allowed
 */
function existsOf(iterable $collection, string $fqcn, bool $invariant = false): bool
{
    return ExistsOfOperation::of($collection)($fqcn, $invariant);
}
