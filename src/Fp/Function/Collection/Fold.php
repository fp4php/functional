<?php

declare(strict_types=1);

namespace Fp\Function\Collection;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TA of TV
 *
 * @psalm-param TA $init
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TV): TV $callback
 *
 * @psalm-return TV
 */
function fold(mixed $init, iterable $collection, callable $callback): mixed
{
    $acc = $init;

    foreach ($collection as $element) {
        $acc = call_user_func($callback, $acc, $element);
    }

    return $acc;
}
