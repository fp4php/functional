<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\LastMapOperation;
use Fp\Operations\LastOperation;

use function Fp\Callable\dropFirstArg;

/**
 * Returns last collection element
 * and None if there is no last element
 *
 * ```php
 * >>> last([1, 2, 3])->get()
 * => 3
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param null|callable(TV): bool $predicate
 * @return Option<TV>
 */
function last(iterable $collection, ?callable $predicate = null): Option
{
    return LastOperation::of($collection)(null !== $predicate ? dropFirstArg($predicate) : null);
}

/**
 * Same as {@see last()} but passing also the key to the $predicate function.
 *
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): bool $predicate
 * @return Option<TV>
 */
function lastKV(iterable $collection, callable $predicate): Option
{
    return LastOperation::of($collection)($predicate);
}

/**
 * A combined {@see last} and {@see map}.
 *
 * Filtering is handled via Option instead of Boolean.
 * So the output type TVO can be different from the input type TV.
 *
 * ```php
 * >>> lastMap(['zero', '1', '2'], fn($elem) => Option::when(is_numeric($elem), fn() => (int) $elem));
 * => Some(2)
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
function lastMap(iterable $collection, callable $callback): Option
{
    return lastMapKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see lastMap()} but passing also the key to the $callback function.
 *
 * @template TK
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): Option<TVO> $callback
 * @return Option<TVO>
 */
function lastMapKV(iterable $collection, callable $callback): Option
{
    return LastMapOperation::of($collection)($callback);
}
