<?php

declare(strict_types=1);

namespace Fp\Collection;

use ArrayAccess;
use Fp\Functional\Option\Option;

/**
 * Find element by its key
 *
 * O(1) for arrays and classes which implement {@see ArrayAccess}
 * O(N) for other cases
 *
 * Returns None if there is no such collection element
 *
 * REPL:
 * >>> at([new Foo(), 2, 3], 1)->get()
 * => Foo|int|null
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
    return Option::some($collection)
        ->filter(fn($coll) => is_array($coll) || $coll instanceof ArrayAccess)
        ->flatMap(fn(array|ArrayAccess $coll) => Option::fromNullable($coll[$key] ?? null))
        ->orElse(fn() => first($collection, fn(mixed $v, mixed $k) => $k === $key));
}

