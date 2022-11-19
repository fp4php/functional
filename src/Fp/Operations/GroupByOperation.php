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
     * @param callable(TK, TV): TKO $f
     * @return HashMap<TKO, NonEmptyHashMap<TK, TV>>
     */
    public function __invoke(callable $f): Map
    {
        /** @psalm-var HashTable<TKO, HashTable<TK, TV>> $groups */
        $groups = new HashTable();

        foreach ($this->gen as $key => $value) {
            $groupKey = $f($key, $value);

            $groups->update(
                $groupKey,
                $groups->get($groupKey)
                    ->map(fn(HashTable $group) => $group->update($key, $value))
                    ->getOrCall(fn() => (new HashTable())->update($key, $value))
            );
        }

        return (new HashMap($groups))
            ->map(function(HashTable $ht) {
                return new NonEmptyHashMap(new HashMap($ht));
            });
    }
}
