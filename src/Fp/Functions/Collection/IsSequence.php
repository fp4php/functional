<?php

declare(strict_types=1);

namespace Fp\Evidence;

use function Fp\Collection\keys;
use function Fp\Collection\reverse;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param 'ASC'|'DESC' $direction
 */
function isSequence(iterable $collection, int $from = 0, string $direction = 'ASC'): bool
{
    $keys = $direction === 'ASC'
        ? keys($collection)
        : keys(reverse($collection));

    $sign = $direction === 'ASC' ? -1 : 1;

    $isSequence = true;
    $previousKey = $from + $sign;

    foreach ($keys as $key) {
        if (!is_int($key) || $key - $previousKey !== -$sign) {
            $isSequence = false;
            break;
        }

        $previousKey = $key;
    }


    return $isSequence;
}

