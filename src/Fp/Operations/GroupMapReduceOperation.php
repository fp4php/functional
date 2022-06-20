<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\Map;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class GroupMapReduceOperation extends AbstractOperation
{
    /**
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return Map<TKO, TVO>
     */
    public function __invoke(callable $group, callable $map, callable $reduce): Map
    {
        /** @psalm-var HashTable<TKO, TVO> $hashTable */
        $hashTable = new HashTable();

        foreach ($this->gen as $item) {
            $key = $group($item);
            $new = $map($item);

            $toReduced =
                /**
                 * @param TVO $old
                 * @return TVO
                 */
                fn(mixed $old) => $reduce($old, $new);

            $hashTable->update(
                $key,
                $hashTable->get($key)
                    ->map($toReduced)
                    ->getOrElse($new)
            );
        }

        return new HashMap($hashTable);
    }
}
