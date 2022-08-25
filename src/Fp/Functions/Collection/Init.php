<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\InitOperation;

/**
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return array<TK, TV>
 * @psalm-return ($collection is list<TV> ? list<TV> : array<TK, TV>)
 */
function init(iterable $collection): array
{
    return InitOperation::of($collection)();
}
