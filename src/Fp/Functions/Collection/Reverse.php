<?php

declare(strict_types=1);

namespace Fp\Collection;

use function Fp\Cast\asArray;

/**
 * Copy collection in reversed order
 *
 * ```php
 * >>> reverse([1, 2, 3]);
 * => [3, 2, 1]
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return array<TK, TV>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? non-empty-list<TV>      :
 *    $collection is list            ? list<TV>                :
 *    $collection is non-empty-array ? non-empty-array<TK, TV> :
 *    array<TK, TV>
 * )
 */
function reverse(iterable $collection): array
{
    return array_reverse(asArray($collection));
}
