<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\MapValuesOperation;

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
 * @param callable(TVI, TK): TVO $callback
 *
 * @return (
 *    $collection is non-empty-list  ? non-empty-list<TVO>      : (
 *    $collection is list            ? list<TVO>                : (
 *    $collection is non-empty-array ? non-empty-array<TK, TVO> : (
 *    array<TK, TVO>
 * ))))
 */
function map(iterable $collection, callable $callback): array
{
    return asArray(MapValuesOperation::of($collection)($callback));
}
