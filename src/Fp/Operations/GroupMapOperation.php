<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyHashMap;
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
     * @return HashMap<TKO, NonEmptyHashMap<TK, TVO>>
     */
    public function __invoke(callable $group, callable $map): Map
    {
        /** @psalm-var HashTable<TKO, HashTable<TK, TVO>> $groups */
        $groups = new HashTable();

        foreach ($this->gen as $key => $value) {
            $groupKey = $group($value);
            $mapped = $map($value);

            $groups->update(
                $groupKey,
                $groups->get($groupKey)
                    ->map(fn(HashTable $group) => $group->update($key, $mapped))
                    ->getOrCall(fn() => (new HashTable())->update($key, $mapped))
            );
        }

        return (new HashMap($groups))
            ->map(function(HashTable $ht) {
                return new NonEmptyHashMap(new HashMap($ht));
            });
    }
}
