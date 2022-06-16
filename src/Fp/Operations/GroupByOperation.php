<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\Nil;

/**
 * @template TK
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends AbstractOperation<TK, TV>
 */
class GroupByOperation extends AbstractOperation
{
    /**
     * @template TKO
     * @psalm-param callable(TV, TK): TKO $f
     * @psalm-return HashMap<TKO, LinkedList<TV>>
     */
    public function __invoke(callable $f): Map
    {
        /**
         * @psalm-var HashTable<TKO, LinkedList<TV>> $hashTable
         */
        $hashTable = new HashTable();

        foreach ($this->gen as $key => $value) {
            $groupKey = $f($value, $key);

            HashTable::update(
                $hashTable,
                $groupKey,
                HashTable::get($hashTable, $groupKey)
                    ->getOrElse(Nil::getInstance())
                    ->prepended($value)
            );
        }

        return new HashMap($hashTable);
    }
}
