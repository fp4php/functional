<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

/**
 * Try copy and cast collection to non-empty-array
 * Returns None if there is no first collection element
 *
 * ```php
 * >>> asNonEmptyArray(LinkedList::collect([1, 2]));
 * => Some([1, 2])
 *
 * >>> asNonEmptyArray(LinkedList::collect([]));
 * => None
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> ...$collections
 * @return Option<non-empty-array<TK, TV>>
 *
 * @no-named-arguments
 */
function asNonEmptyArray(iterable ...$collections): Option
{
    $array = asArray(...$collections);

    return !empty($array)
        ? Option::some($array)
        : Option::none();
}
