<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Returns every collection element except first
 *
 * REPL:
 * >>> tail([1, 2, 3]);
 * => [2, 3]
 *
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return array<TK, TV>
 */
function tail(iterable $collection): array
{
    $resultCollection = [];
    $toggle = false;

    foreach ($collection as $index => $element) {
        if ($toggle) {
            $resultCollection[$index] = $element;
        }

        $toggle = true;
    }

    return $resultCollection;
}
