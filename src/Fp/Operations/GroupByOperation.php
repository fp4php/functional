<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashMap;
use Fp\Collections\HashMapBuffer;
use Fp\Collections\HashTable;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\Nil;
use Fp\Functional\Option\Option;
use Fp\Functional\State\State;
use Fp\Functional\State\StateFunctions;

use function Fp\unit;

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
        $state = StateFunctions::set($init);

        return State::doA($init, function() use ($state, $f) {
            foreach ($this->gen as $key => $value) {
                $groupKey = $f($value, $key);
                $group = yield $state
                    ->inspect(fn(HashTable $tbl) => HashMapBuffer::get($tbl, $groupKey))
                    ->map(fn(Option $group) => $group->getOrElse(Nil::getInstance()));

                yield HashMapBuffer::update($groupKey, $group->prepended($value));
            }

            return yield $state
                ->inspect(fn(HashTable $tbl) => new HashMap($tbl, empty($tbl->table)));
        });
    }
}
