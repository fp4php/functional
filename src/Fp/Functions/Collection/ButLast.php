<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\ButLastOperation;

/**
 * @template TK
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return (
 *    $collection is non-empty-list<TV>      ? non-empty-list<TV>      :
 *    $collection is list<TV>                ? list<TV>                :
 *    $collection is non-empty-array<TK, TV> ? non-empty-array<TK, TV> :
 *    array<TK, TV>
 * )
 */
function butLast(iterable $collection): array
{
    return ButLastOperation::of($collection)();
}
