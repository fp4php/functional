<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param \Closure(TV, TK): bool $callback
 *
 * @psalm-return bool
 */
function every(iterable $collection, \Closure $callback, bool $strict = true): bool
{
    $result = !$strict;

    foreach ($collection as $index => $element) {
        $result = true;

        if (!$callback($element, $index)) {
            $result = false;
            break;
        }
    }

    return $result;
}
