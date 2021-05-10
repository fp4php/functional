<?php

declare(strict_types=1);

namespace Fp\Cast;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return list<TV>
 */
function asList(iterable $collection): array
{
    $aggregate = [];

    foreach ($collection as $element) {
        $aggregate[] = $element;
    }

    return $aggregate;
}
