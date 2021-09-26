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
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return array<TK, TV>
 */
function reverse(iterable $collection): array
{
    return array_reverse(asArray($collection));
}
