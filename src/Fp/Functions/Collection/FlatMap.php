<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Given [1, 4] and fn(int $x) => [$x - 1, $x, $x + 1]
 * Returns [0, 1, 2, 3, 4, 5]
 *
 * Consists of map and flatten operations:
 * 1) map [1, 4] to [[0, 1, 2], [3, 4, 5]]
 * 2) flatten [[0, 1, 2], [3, 4, 5]] to [0, 1, 2, 3, 4, 5]
 *
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param callable(TVI, TK): iterable<array-key, TVO> $callback
 *
 * @psalm-return list<TVO>
 */
function flatMap(iterable $collection, callable $callback): array
{
    $flattened = [];

    foreach ($collection as $index => $element) {
        $result = call_user_func($callback, $element, $index);

        foreach ($result as $item) {
            $flattened[] = $item;
        }
    }

    return $flattened;
}
