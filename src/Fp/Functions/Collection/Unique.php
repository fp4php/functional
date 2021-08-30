<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Returns collection unique elements
 *
 * REPL:
 * >>> unique([1, 2, 2, 3, 3, 3, 3]);
 * => [1, 2, 3]
 * >>> unique(
 *     [new User(id: 1), new User(id: 1), new User(id: 2)],
 *     fn(User $user) => $user->getId()
 * );
 * => [User(1), User(2)]
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV): array-key $callback returns element unique id
 *
 * @psalm-return list<TV>
 */
function unique(iterable $collection, callable $callback): array
{
    $hashTable = [];
    $aggregation = [];

    foreach ($collection as $element) {
        $elementHash = $callback($element);
        $isPresent = isset($hashTable[$elementHash]);

        if (!$isPresent) {
            $aggregation[] = $element;
            $hashTable[$elementHash] = true;
        }
    }

    return $aggregation;
}
