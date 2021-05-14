<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * @psalm-template TK
 *
 * @psalm-param iterable<TK, int|string> $collection
 * @psalm-param 'ASC'|'DESC' $direction
 */
function isSequence(iterable $collection, int $from = 0, string $direction = 'ASC'): bool
{
    $isSequence = true;
    $sign = $direction === 'ASC' ? -1 : 1;
    $previousElement = $from + $sign;

    foreach ($collection as $element) {
        if (!is_int($element) || $element - $previousElement !== -$sign) {
            $isSequence = false;
            break;
        }

        $previousElement = $element;
    }


    return $isSequence;
}

