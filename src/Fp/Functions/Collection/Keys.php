<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Returns list of collection keys
 *
 * REPL:
 * >>> keys(['a' => 1, 'b' => 2]);
 * => ['a', 'b']
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return ($collection is non-empty-array ? non-empty-list<TK> : list<TK>)
 */
function keys(iterable $collection): array
{
    $keys = [];

    foreach ($collection as $index => $element) {
        $keys[] = $index;
    }

    return $keys;
}
