<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Produces a new array of elements by mapping each element in collection
 * through a transformation function (callback).
 *
 * Keys are preserved
 *
 * REPL:
 * >>> map([1, 2, 3], fn(int $v) => (string) $v);
 * => ['1', '2', '3']
 *
 *
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param callable(TVI, TK): TVO $callback
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? non-empty-list<TVO>        : (
 *    $collection is list            ? list<TVO>                  : (
 *    $collection is non-empty-array ? non-empty-array<TK, TVO> : (
 *    array<TK, TVO>
 * ))))
 */
function map(iterable $collection, callable $callback): array
{
    $aggregation = [];

    foreach ($collection as $index => $element) {
        $aggregation[$index] = call_user_func($callback, $element, $index);
    }

    return $aggregation;
}
