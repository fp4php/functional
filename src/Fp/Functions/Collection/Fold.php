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
 * @template TInit
 *
 * @param TInit $init
 * @param iterable<TV> $collection
 * @return FoldOperation<TV, TInit>
 */
function fold(mixed $init, iterable $collection): FoldOperation
{
    return new FoldOperation($collection, $init);
}
