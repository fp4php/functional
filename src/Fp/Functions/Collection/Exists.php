<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\ExistsOfOperation;
use Fp\Operations\ExistsOperation;
use function Fp\Callable\dropFirstArg;

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
 * @param callable(TV): bool $predicate
 */
function exists(iterable $collection, callable $predicate): bool
{
    return existsKV($collection, dropFirstArg($predicate));
}

/**
 * Same as {@see exists()} but passing also the key to the $predicate function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 */
function existsKV(iterable $collection, callable $predicate): bool
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
 * @psalm-assert-if-true list<TVO> $collection
 *
 * @param iterable<TK, TV> $collection
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn
 */
function existsOf(iterable $collection, string|array $fqcn, bool $invariant = false): bool
{
    return ExistsOfOperation::of($collection)($fqcn, $invariant);
}
