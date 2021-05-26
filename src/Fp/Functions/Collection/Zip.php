<?php

declare(strict_types=1);

namespace Fp\Collection;

use function Fp\Cast\asList;

/**
 * Returns a iterable collection formed from this iterable collection
 * and another iterable collection by combining corresponding elements in pairs.
 *
 * If one of the two collections is longer than the other,
 * its remaining elements are ignored.
 *
 * REPL:
 * >>> zip([1, 2, 3], ['a', 'b']);
 * => [[1, 'a'], [2, 'b']]
 *
 *
 * @psalm-template TKL of array-key
 * @psalm-template TVL
 * @psalm-template TKR of array-key
 * @psalm-template TVR
 *
 * @psalm-param iterable<TKL, TVL> $left first collection
 * @psalm-param iterable<TKR, TVR> $right second collection
 *
 * @psalm-return list<array{TVL, TVR}>
 */
function zip(iterable $left, iterable $right): array
{
    $aggregation = [];
    $leftList = asList($left);
    $rightList = asList($right);

    foreach ($leftList as $leftKey => $leftValue) {
        if (isset($rightList[$leftKey])) {
            $aggregation[] = [$leftValue, $rightList[$leftKey]];
        }
    }

    return $aggregation;
}
