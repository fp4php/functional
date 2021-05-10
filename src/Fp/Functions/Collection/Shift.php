<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Functional\Tuple\Tuple2;

use function Fp\Cast\asNonEmptyList;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<Tuple2<TV, list<TV>>>
 */
function shift(iterable $collection): Option
{
    return asNonEmptyList($collection)
        ->map(fn($list) => new Tuple2(
            first: array_shift($list),
            second: $list
        ));
}
