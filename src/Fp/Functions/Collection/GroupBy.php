<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\Collection;
use Fp\Collections\NonEmptyHashMap;
use Fp\Operations\GroupByOperation;
use function Fp\Callable\dropFirstArg;

/**
 * Group collection elements by key returned by function
 *
 * ```php
 * >>> groupBy(
 *     [1, 2, 3],
 *     fn(int $v): int => $v
 * );
 * => [1 => [1], 2 => [2], 3 => [3]]
 * ```
 *
 * @template TV
 * @template TKO of array-key
 *
 * @param Collection<TV> | iterable<TV> $collection
 * @param callable(TV): TKO $callback
 * @return (
 *     $collection is non-empty-array
 *          ? non-empty-array<TKO, non-empty-list<TV>>
 *          : array<TKO, non-empty-list<TV>>
 * )
 */
function groupBy(iterable $collection, callable $callback): array
{
    return groupByKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see groupBy()} but passing also the key to the $callback function.
 *
 * @template TK
 * @template TV
 * @template TKO of array-key
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): TKO $callback
 * @return (
 *     $collection is non-empty-array
 *          ? non-empty-array<TKO, non-empty-list<TV>>
 *          : array<TKO, non-empty-list<TV>>
 * )
 */
function groupByKV(iterable $collection, callable $callback): array
{
    return GroupByOperation::of($collection)($callback)
        ->map(fn(NonEmptyHashMap $group) => $group->values()->toNonEmptyList())
        ->toArray();
}
