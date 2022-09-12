<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\MkStringOperation;

/**
 * Displays all elements of this collection in a string
 * using start, end, and separator strings.
 *
 * ```php
 * >>> mkString([1, 2, 3])
 * => '1,2,3'
 *
 * >>> mkString([1, 2, 3], '(', ', ', ')')
 * => '(1, 2, 3)'
 *
 * >>> mkString([], '(', ', ', ')')
 * => '()'
 * ```
 */
function mkString(iterable $collection, string $start = '', string $sep = ',', string $end = ''): string
{
    return MkStringOperation::of($collection)($start, $sep, $end);
}
