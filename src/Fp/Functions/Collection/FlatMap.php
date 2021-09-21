<?php

declare(strict_types=1);

namespace Fp\Collection;

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
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param callable(TVI, TK): iterable<TVO> $callback
 * @psalm-return list<TVO>
 */
function flatMap(iterable $collection, callable $callback): array
{
    $flattened = [];

    foreach ($collection as $index => $element) {
        $result = $callback($element, $index);

        foreach ($result as $item) {
            $flattened[] = $item;
        }
    }

    return $flattened;
}
