<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @internal
 * @template TK
 * @template TV
 * @psalm-type hash = string
 * @psalm-suppress ImpureMethodCall, ImpurePropertyFetch
 */
final class HashTable
{
    /**
     * @var array<hash, list<array{TK, TV}>>
     */
    public array $table = [];

    /**
     * @template TKey
     * @template TValue
     * @param HashTable<TKey, TValue> $hashTable
     * @param TKey $key
     * @return Option<TValue>
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
     * @template TKey
     * @template TValue
     * @param TKey $key
     * @param TValue $value
     * @param HashTable<TKey, TValue> $hashTable
     * @return HashTable<TKey, TValue>
     * @psalm-suppress PropertyTypeCoercion
     */
    public static function update(HashTable $hashTable, mixed $key, mixed $value): HashTable
    {
        $hash = (string) HashComparator::computeHash($key);

        if (!isset($hashTable->table[$hash])) {
            $hashTable->table[$hash] = [];
        }

        $replacedPos = -1;

        foreach ($hashTable->table[$hash] as $idx => [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $replacedPos = $idx;
                $hashTable->table[$hash][$idx][1] = $value;
            }
        }

        if ($replacedPos < 0) {
            $hashTable->table[$hash][] = [$key, $value];
        }

        return $hashTable;
    }
}
