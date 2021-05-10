<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return array<TK, TV>
 */
function asArray(iterable $collection): array
{
    $aggregate = [];

    foreach ($collection as $index => $element) {
        $aggregate[$index] = $element;
    }

    return $aggregate;
}
