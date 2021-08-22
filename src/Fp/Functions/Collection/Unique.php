<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\Seq;

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
 * @deprecated use {@see Set} or {@see Seq::unique()}
 * @psalm-template TK of array-key
 * @psalm-template TV of (object|scalar)
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param null|(callable(TV): (int|string)) $callback returns element unique id
 *
 * @psalm-return list<TV>
 */
function unique(iterable $collection, ?callable $callback = null): array
{
    if (is_null($callback)) {
        $callback = fn(object|int|float|bool|string $element): string => match (true) {
            is_object($element) => spl_object_hash($element),
            default => (string) $element,
        };
    }

    $hashTable = [];
    $aggregation = [];

    foreach ($collection as $element) {
        $elementHash = call_user_func($callback, $element);
        $isPresent = isset($hashTable[$elementHash]);

        if (!$isPresent) {
            $aggregation[] = $element;
            $hashTable[$elementHash] = true;
        }
    }

    return $aggregation;
}
