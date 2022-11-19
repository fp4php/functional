<?php

declare(strict_types=1);

namespace Fp\Operations;

use Closure;
use Fp\Collections\HashTable;
use Fp\Functional\Option\Option;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class TraverseOptionOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $f
     * @return Option<Generator<TK, TVO>>
     */
    public function __invoke(callable $f): Option
    {
        /** @psalm-var HashTable<TK, TVO> $hashTable */
        $hashTable = new HashTable();

        foreach ($this->gen as $key => $value) {
            $mapped = $f($key, $value);

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
     * @param iterable<TKI, Option<TVI> | Closure(): Option<TVI>> $collection
     * @return Option<Generator<TKI, TVI>>
     */
    public static function id(iterable $collection): Option
    {
        return self::of($collection)(
            fn(mixed $_key, Option|Closure $i): Option => $i instanceof Closure ? $i() : $i,
        );
    }
}
