<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\FirstMapOperation;
use Fp\Operations\FirstOperation;
use function Fp\Callable\dropFirstArg;

/**
 * Find first element which satisfies the condition
 *
 * ```php
 * >>> first([1, 2], fn(int $v): bool => $v === 2)->get()
 * => 1
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param null|callable(TV): bool $predicate
 * @return Option<TV>
 */
function first(iterable $collection, ?callable $predicate = null): Option
{
    return FirstOperation::of($collection)(null !== $predicate ? dropFirstArg($predicate) : null);
}

/**
 * Same as {@see first()} but passing also the key to the $predicate function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 * @return Option<TV>
 */
function firstKV(iterable $collection, callable $predicate): Option
{
    return FirstOperation::of($collection)($predicate);
}

/**
 * A combined {@see first} and {@see map}.
 *
 * Filtering is handled via Option instead of Boolean.
 * So the output type TVO can be different from the input type TV.
 *
 * ```php
 * >>> firstMap(['zero', '1', '2'], fn($elem) => Option::when(is_numeric($elem), fn() => (int) $elem));
 * => Some(1)
 * ```
 *
 * @template TK
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): Option<TVO> $callback
 * @return Option<TVO>
 */
function firstMap(iterable $collection, callable $callback): Option
{
    return firstMapKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see firstMap()} but passing also the key to the $callback function.
 *
 * @template TK
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): Option<TVO> $callback
 * @return Option<TVO>
 */
function firstMapKV(iterable $collection, callable $callback): Option
{
    return FirstMapOperation::of($collection)($callback);
}
