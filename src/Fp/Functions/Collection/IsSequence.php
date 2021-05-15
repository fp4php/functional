<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Check if collection is ascending or descending integer sequence
 * from given start value
 *
 * @psalm-template TK
 *
 * @psalm-param iterable<TK, int|string> $collection
 * @psalm-param 'ASC'|'DESC' $direction
 *
 * @psalm-return bool
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

/**
 * Check if collection is non empty ascending or descending integer sequence
 * from given start value
 *
 * @psalm-template TK
 *
 * @psalm-param iterable<TK, int|string> $collection
 * @psalm-param 'ASC'|'DESC' $direction
 *
 * @psalm-return bool
 */
function isNonEmptySequence(iterable $collection, int $from = 0, string $direction = 'ASC'): bool
{
    $empty = true;
    $isSequence = true;
    $sign = $direction === 'ASC' ? -1 : 1;
    $previousElement = $from + $sign;

    foreach ($collection as $element) {
        $empty = false;

        if (!is_int($element) || $element - $previousElement !== -$sign) {
            $isSequence = false;
            break;
        }

        $previousElement = $element;
    }


    return $isSequence && !$empty;
}

;

