<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;

use function Fp\Cast\asNonEmptyList;

/**
 * Shift first collection element
 * and return tuple containing this element and other collection elements
 * If there is no first element then returns None
 *
 * ```php
 * >>> [$head, $tail] = shift([1, 2, 3])->get();
 * => [1, [2, 3]]
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @return Option<array{TV, list<TV>}>
 */
function shift(iterable $collection): Option
{
    return asNonEmptyList($collection)
        ->map(fn($list) => [
            array_shift($list),
            $list
        ]);
}
