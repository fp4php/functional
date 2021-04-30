<?php

declare(strict_types=1);

namespace Fp\Function;

/**
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
