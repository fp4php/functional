<?php

declare(strict_types=1);

namespace Fp\Collection;

use ArrayAccess;
use Fp\Functional\Option\Option;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param TK $key
 *
 * @psalm-return Option<TV>
 */
function at(iterable $collection, int|string $key): Option
{
    if (is_array($collection) || $collection instanceof ArrayAccess) {
        return Option::of($collection[$key] ?? null);
    } else {
        /** @psalm-suppress UnusedClosureParam */
        return first(
            $collection,
            fn(mixed $v, mixed $k) => $k === $key);
    }
}

