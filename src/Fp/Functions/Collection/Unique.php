<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\HashMap;
use Generator;

/**
 * Returns collection unique elements
 *
 * ```php
 * >>> unique([1, 2, 2, 3, 3, 3, 3]);
 * => [1, 2, 3]
 *
 * >>> unique(
 *     [new User(id: 1), new User(id: 1), new User(id: 2)],
 *     fn(User $user) => $user->getId()
 * );
 * => [User(1), User(2)]
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV): array-key $callback returns element unique id
 * @psalm-return list<TV>
 */
function unique(iterable $collection, callable $callback): array
{
    $source = function () use ($callback, $collection): Generator {
        foreach ($collection as $elem) {
            yield $callback($elem) => $elem;
        }
    };

    return HashMap::collect($source())->values()->toArray();
}
