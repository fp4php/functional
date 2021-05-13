<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return list<int|string>
 *
 * @psalm-suppress UnusedVariable
 */
function keys(iterable $collection): array
{
    $keys = [];

    foreach ($collection as $index => $element) {
        $keys[] = $index;
    }

    return $keys;
}
