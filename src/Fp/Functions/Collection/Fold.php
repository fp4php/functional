<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\FoldOperation;

/**
 * Fold many elements into one
 *
 * ```php
 * >>> fold('', ['a', 'b', 'c'])(fn($acc, $curr) => $acc . $cur);
 * => 'abc'
 * ```
 *
 * @template TV
 * @template TA
 *
 * @param TA $init
 * @param iterable<TV> $collection
 * @return FoldOperation<TV, TA>
 */
function fold(mixed $init, iterable $collection): FoldOperation
{
    return new FoldOperation($collection, $init);
}
