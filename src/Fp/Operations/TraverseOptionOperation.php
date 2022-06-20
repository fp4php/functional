<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashTable;
use Fp\Functional\Option\Option;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
class TraverseOptionOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $f
     * @return Option<Generator<TK, TVO>>
     */
    public function __invoke(callable $f): Option
    {
        /** @psalm-var HashTable<TK, TVO> $hashTable */
        $hashTable = new HashTable();

        foreach ($this->gen as $key => $value) {
            $mapped = $f($value);

            if ($mapped->isNone()) {
                return Option::none();
            }

            $hashTable->update($key, $mapped->get());
        }

        return Option::some($hashTable->getKeyValueIterator());
    }

    /**
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, Option<TVI>> $collection
     * @return Option<Generator<TKI, TVI>>
     */
    public static function id(iterable $collection): Option
    {
        $id =
            /**
             * @param Option<TVI> $I
             * @return Option<TVI>
             */
            fn(Option $i): Option => $i;

        return self::of($collection)($id);
    }
}
