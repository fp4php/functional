<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool $callback
 *
 * @psalm-return bool
 */
function every(iterable $collection, callable $callback, bool $strict = true): bool
{
    $result = !$strict;

    foreach ($collection as $index => $element) {
        $result = true;

        if (!call_user_func($callback, $element, $index)) {
            $result = false;
            break;
        }
    }

    return $result;
}
