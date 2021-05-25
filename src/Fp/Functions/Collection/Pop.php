<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;

use function Fp\Cast\asNonEmptyList;

/**
 * Pop last collection element
 * and return tuple containing this element and other collection elements
 * If there is no last element then returns None
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<array{TV, list<TV>}>
 */
function pop(iterable $collection): Option
{
    return asNonEmptyList($collection)
        ->map(fn($list) => [
            array_pop($list),
            $list
        ]);
}
