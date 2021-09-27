<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\Nil;
use Fp\Functional\Option\Option;
use Fp\Functional\State\State;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
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
         * @psalm-var HashTable<TKO, LinkedList<TV>> $init
         */
        $init = new HashTable();
        $state = State::setState($init);

        $hashTable = State::forS($init, function() use ($state, $f) {
            foreach ($this->gen as $key => $value) {
                $groupKey = $f($value, $key);
                $group = yield $state
                    ->inspect(fn(HashTable $tbl) => HashTable::get($tbl, $groupKey))
                    ->map(fn(Option $group) => $group->getOrElse(Nil::getInstance()));

                HashTable::update(yield $state->get(), $groupKey, $group->prepended($value));
            }
        });

        return new HashMap($hashTable, empty($hashTable->table));
    }
}
