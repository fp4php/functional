<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\InitOperation;

/**
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return ($collection is list<TV> ? list<TV> : array<TK, TV>)
 */
function init(iterable $collection): array
{
    return InitOperation::of($collection)();
}
