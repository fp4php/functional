<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\ZipOperation;

use function Fp\Cast\asList;

/**
 * Returns an iterable collection formed from this iterable collection
 * and another iterable collection by combining corresponding elements in pairs.
 *
 * If one of the two collections is longer than the other,
 * its remaining elements are ignored.
 *
 * ```php
 * >>> zip([1, 2, 3], ['a', 'b']);
 * => [[1, 'a'], [2, 'b']]
 * ```
 *
 * @template TKL of array-key
 * @template TVL
 * @template TKR of array-key
 * @template TVR
 *
 * @param iterable<TKL, TVL> $left first collection
 * @param iterable<TKR, TVR> $right second collection
 * @return list<array{TVL, TVR}>
 */
function zip(iterable $left, iterable $right): array
{
    return asList(ZipOperation::of($left)($right));
}
