<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Functional\State\State;
use Fp\Functional\State\StateFunctions;
use Fp\Functional\Unit;

use function Fp\Callable\partial;

/**
 * Internal buffer
 * Which provides fast update operation
 *
 * @internal
 */
final class HashMapBuffer
{
    /**
     * @template TK
     * @template TV
     * @param HashTable<TK, TV> $hashTable
     * @param TK $key
     * @return Option<TV>
     */
    public static function get(HashTable $hashTable, mixed $key): Option
    {
        $hash = (string) HashComparator::computeHash($key);
        $elem = null;

        foreach ($hashTable->table[$hash] ?? [] as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }

    /**
     * @psalm-pure
     * @template TK
     * @template TV
     * @param TK $key
     * @param TV $value
     * @return State<HashTable<TK, TV>, Unit>
     */
    public static function update(mixed $key, mixed $value): State
    {
        return StateFunctions::modify(partial([self::class, 'modify'], $key, $value));
    }

    /**
     * @template TK
     * @template TV
     * @param TK $key
     * @param TV $value
     * @param HashTable<TK, TV> $hashTable
     * @return HashTable<TK, TV>
     */
    public static function modify(mixed $key, mixed $value, HashTable $hashTable): HashTable
    {
        $hash = (string) HashComparator::computeHash($key);

        if (!isset($hashTable->table[$hash])) {
            $hashTable->table[$hash] = [];
        }

        $replacedPos = -1;

        foreach ($hashTable->table[$hash] as $idx => [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $replacedPos = $idx;
                $pairValueRef =& $hashTable->table[$hash][$idx][1];
                $pairValueRef = $value;
            }
        }

        if ($replacedPos < 0) {
            $hashTable->table[$hash][] = [$key, $value];
        }

        return $hashTable;
    }
}
