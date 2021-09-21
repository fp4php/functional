<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * Copy iterable as array
 *
 * ```php
 * >>> asArray(LinkedList::collect([1, 2]));
 * => [1, 2]
 * ```
 *
 * @psalm-template TP of bool
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param TP $preserveKeys
 * @psalm-return (TP is true ? array<TK, TV> : array<int, TV>)
 */
function asArray(iterable $collection, bool $preserveKeys = true): array
{
    $aggregate = [];

    foreach ($collection as $index => $element) {
        if ($preserveKeys) {
            $aggregate[$index] = $element;
        } else {
            $aggregate[] = $element;
        }
    }

    return $aggregate;
}
