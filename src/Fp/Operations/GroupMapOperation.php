<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyLinkedList;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class GroupMapOperation extends AbstractOperation
{
    /**
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     *
     * @return HashMap<TKO, NonEmptyLinkedList<TVO>>
     */
    public function __invoke(callable $group, callable $map): Map
    {
        /** @psalm-var HashTable<TKO, NonEmptyLinkedList<TVO>> $hashTable */
        $hashTable = new HashTable();

        foreach ($this->gen as $value) {
            $key = $group($value);
            $new = $map($value);

            $hashTable->update(
                $key,
                $hashTable->get($key)
                    ->map(fn(NonEmptyLinkedList $group) => $group->prepended($new))
                    ->getOrCall(fn() => NonEmptyLinkedList::collectNonEmpty([$new]))
            );
        }

        return new HashMap($hashTable);
    }
}
