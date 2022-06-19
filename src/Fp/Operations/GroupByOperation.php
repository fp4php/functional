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
class GroupByOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TV): TKO $f
     * @return HashMap<TKO, NonEmptyLinkedList<TV>>
     */
    public function __invoke(callable $f): Map
    {
        /** @psalm-var HashTable<TKO, NonEmptyLinkedList<TV>> $hashTable */
        $hashTable = new HashTable();

        foreach ($this->gen as $value) {
            $groupKey = $f($value);

            $hashTable->update(
                $groupKey,
                $hashTable->get($groupKey)
                    ->map(fn(NonEmptyLinkedList $group) => $group->prepended($value))
                    ->getOrCall(fn() => NonEmptyLinkedList::collectNonEmpty([$value]))
            );
        }

        return new HashMap($hashTable);
    }
}
