<?php

declare(strict_types=1);

namespace Fp\Collection;

use ArrayAccess;
use Fp\Functional\Option\Option;

/**
 * Find element by it's key
 *
 * O(1) for arrays and classes which implement {@see ArrayAccess}
 * O(N) for other cases
 *
 * Returns None if there is no such collection element
 *
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
        return Option::fromNullable($collection[$key] ?? null);
    } else {
        /** @psalm-suppress UnusedClosureParam */
        return first(
            $collection,
            fn(mixed $v, mixed $k) => $k === $key);
    }
}

