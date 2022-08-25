<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\TailOperation;

use function Fp\Cast\asList;

/**
 * Returns every collection element except first
 *
 * ```php
 * >>> tail([1, 2, 3]);
 * => [2, 3]
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @return list<TV>
 */
function tail(iterable $collection): array
{
    return asList(TailOperation::of($collection)());
}
