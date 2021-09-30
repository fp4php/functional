<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashTable;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\Nil;
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

        foreach ($this->gen as $key => $value) {
            $groupKey = $f($value, $key);
            $state = $state
                ->inspect(fn(HashTable $tbl) => [
                    $tbl,
                    HashTable::get($tbl, $groupKey)->getOrElse(Nil::getInstance())
                ])
                ->map(fn(array $pair) => HashTable::update(
                    $pair[0],
                    $groupKey,
                    $pair[1]->prepended($value)
                ));
        }

        return new HashMap($state->runS($init));
    }
}
