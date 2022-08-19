<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\MapOperation;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asArray;

/**
 * Produces a new array of elements by mapping each element in collection
 * through a transformation function (callback).
 *
 * Keys are preserved
 *
 * ```php
 * >>> map([1, 2, 3], fn(int $v) => (string) $v);
 * => ['1', '2', '3']
 * ```
 *
 *
 * @template TK of array-key
 * @template TVI
 * @template TVO
 *
 * @param iterable<TK, TVI> $collection
 * @param callable(TVI): TVO $callback
 *
 * @return (
 *    $collection is non-empty-list<TVI>      ? non-empty-list<TVO>      :
 *    $collection is list<TVI>                ? list<TVO>                :
 *    $collection is non-empty-array<TK, TVI> ? non-empty-array<TK, TVO> :
 *    array<TK, TVO>
 * )
 */
function map(iterable $collection, callable $callback): array
{
    return mapKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see map()}, but passing also the key to the $callback function.
 *
 * ```php
 * >>> mapKV(['one' => 1, 'two' => 2, 'three' => 3], fn(string $k, int $v) => "{$k}-{$v}");
 * => ['one-1', 'two-2', 'three-3']
 * ```
 *
 *
 * @template TK of array-key
 * @template TVI
 * @template TVO
 *
 * @param iterable<TK, TVI> $collection
 * @param callable(TK, TVI): TVO $callback
 *
 * @return (
 *    $collection is non-empty-list<TVI>      ? non-empty-list<TVO>      :
 *    $collection is list<TVI>                ? list<TVO>                :
 *    $collection is non-empty-array<TK, TVI> ? non-empty-array<TK, TVO> :
 *    array<TK, TVO>
 * )
 */
function mapKV(iterable $collection, callable $callback): array
{
    return asArray(MapOperation::of($collection)($callback));
}
