<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Functional\Tuple\Tuple2;

use function Fp\Cast\asNonEmptyList;

/**
 * Pop last collection element
 * and return Tuple2 containing this element and other collection elements
 * If there is no last element then returns None
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<Tuple2<TV, list<TV>>>
 */
function pop(iterable $collection): Option
{
    return asNonEmptyList($collection)
        ->map(fn($list) => new Tuple2(
            first: array_pop($list),
            second: $list
        ));
}
