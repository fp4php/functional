<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\FlatMapOperation;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asList;

/**
 * Map + flatten combination
 *
 * <ul>
 *     <li> map [1, 4] to [[0, 1, 2], [3, 4, 5]] </li>
 *     <li> flatten [[0, 1, 2], [3, 4, 5]] to [0, 1, 2, 3, 4, 5] </li>
 * </ul>
 *
 * ```php
 * >>> flatMap([1, 4], fn(int $x) => [$x - 1, $x, $x + 1]);
 * => [0, 1, 2, 3, 4, 5]
 * ```
 *
 * @template TV
 * @template TVO
 *
 * @param iterable<TV> $collection
 * @param callable(TV): iterable<TVO> $callback
 * @return list<TVO>
 */
function flatMap(iterable $collection, callable $callback): array
{
    return flatMapKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see flatMap()} but passing also the key to the $callback function.
 *
 * @template TK
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): iterable<TVO> $callback
 * @return list<TVO>
 */
function flatMapKV(iterable $collection, callable $callback): array
{
    return asList(FlatMapOperation::of($collection)($callback));
}
