<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * Copy collection as list
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> ...$collections
 *
 * @psalm-return list<TV>
 */
function asList(iterable ...$collections): array
{
    $aggregate = [];

    foreach ($collections as $collection) {
        foreach ($collection as $element) {
            $aggregate[] = $element;
        }
    }

    return $aggregate;
}
