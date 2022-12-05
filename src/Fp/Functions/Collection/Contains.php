<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Checks if a value exists in an array.
 *
 * ```php
 * >>> contains(42, [40, 41, 42]);
 * => true
 * >>> contains(43, [40, 41, 42]);
 * => false
 * ```
 */
function contains(mixed $elem, iterable $collection): bool
{
    /** @var mixed $item */
    foreach ($collection as $item) {
        if ($item === $elem) {
            return true;
        }
    }

    return false;
}

/**
 * Varargs version of {@see contains()}
 */
function containsT(mixed $elem, mixed ...$items): bool
{
    return contains($elem, $items);
}
