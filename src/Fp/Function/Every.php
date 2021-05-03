<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param \Closure(TV, TK, iterable<TK, TV>): bool $callback
 *
 * @psalm-return bool
 */
function every($collection, \Closure $callback, bool $strict = true): bool
{
    $result = !$strict;

    foreach ($collection as $index => $element) {
        $result = true;

        if (!$callback($element, $index, $collection)) {
            $result = false;
            break;
        }
    }

    return $result;
}
