<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Returns every collection elements except last one
 *
 * REPL:
 * >>> butLast(['a' => 1, 2, 3]);
 * => ['a' => 1, 2]
 *
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return (
 *     $collection is non-empty-list ? list<TV> : (
 *     $collection is list ? list<TV> : (
 *     array<TK, TV>
 * )))
 */
function butLast(iterable $collection): array
{
    $aggregation = [];
    $previousValue = null;
    $previousIndex = null;
    $toggle = false;

    foreach ($collection as $index => $element) {
        if ($toggle) {
            /** @psalm-suppress PossiblyNullArrayOffset */
            $aggregation[$previousIndex] = $previousValue;
        }

        $previousIndex = $index;
        $previousValue = $element;
        $toggle = true;
    }

    return $aggregation;
}
