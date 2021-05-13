<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param 'ASC'|'DESC' $direction
 */
function isSequence(iterable $collection, int $from = 0, string $direction = 'ASC'): bool
{
    $elements = $direction === 'ASC'
        ? $collection
        : reverse($collection);

    $isSequence = true;
    $previousElement = $from - 1;

    foreach ($elements as $element) {
        if (!is_int($element) || $element - $previousElement !== 1) {
            $isSequence = false;
            break;
        }

        $previousElement = $element;
    }


    return $isSequence;
}

