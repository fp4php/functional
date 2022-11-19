<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\EveryOperation;

use function Fp\Callable\dropFirstArg;

/**
 * Returns true if every collection element satisfies the condition
 * false otherwise
 *
 * ```php
 * >>> every([1, 2], fn(int $v) => $v === 1);
 * => false
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param callable(TV): bool $predicate
 */
function every(iterable $collection, callable $predicate): bool
{
    return everyKV($collection, dropFirstArg($predicate));
}

/**
 * Same as {@see every()} but passing also the key to the $predicate function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 */
function everyKV(iterable $collection, callable $predicate): bool
{
    return EveryOperation::of($collection)($predicate);
}
