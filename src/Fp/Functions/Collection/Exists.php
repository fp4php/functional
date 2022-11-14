<?php

declare(strict_types=1);

namespace Fp\Collection;

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
