<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyHashMap;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class GroupByOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TV): TKO $f
     * @return HashMap<TKO, NonEmptyHashMap<TK, TV>>
     */
    public function __invoke(callable $f): Map
    {
        /** @psalm-var HashTable<TKO, NonEmptyHashMap<TK, TV>> $hashTable */
        $hashTable = new HashTable();

        foreach ($this->gen as $key => $value) {
            $groupKey = $f($value);

            $hashTable->update(
                $groupKey,
                $hashTable->get($groupKey)
                    ->map(fn(NonEmptyHashMap $group) => $group->updated($key, $value))
                    ->getOrCall(fn() => NonEmptyHashMap::collectPairsNonEmpty([[$key, $value]]))
            );
        }

        return new HashMap($hashTable);
    }
}
