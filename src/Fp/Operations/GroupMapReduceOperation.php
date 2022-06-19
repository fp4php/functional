<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\Map;
use Generator;

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
            $out = $map($item);

            $toReduced =
                /**
                 * @param TVO $lhs
                 * @return TVO
                 */
                fn(mixed $lhs) => $reduce($lhs, $out);

            $hashTable->update(
                $key,
                $hashTable->get($key)
                    ->map($toReduced)
                    ->getOrElse($out)
            );
        }

        return new HashMap($hashTable);
    }
}
