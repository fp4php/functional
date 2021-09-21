<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Returns every collection element except first
 *
 * ```php
 * >>> tail([1, 2, 3]);
 * => [2, 3]
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return list<TV>
 */
function tail(iterable $collection): array
{
    $buffer = [];
    $toggle = false;

    foreach ($collection as $element) {
        if ($toggle) {
            $buffer[] = $element;
        }

        $toggle = true;
    }

    return $buffer;
}
