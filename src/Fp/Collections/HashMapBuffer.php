<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * Provides fast update operation
 *
 * @internal
 * @template TK
 * @template TV
 */
final class HashMapBuffer
{
    /**
     * @var HashTable<TK, TV>
     */
    private HashTable $hashTable;

    public function __construct()
    {
        $this->hashTable = new HashTable();
    }

    /**
     * @param TK $key
     */
    public function get(mixed $key): Option
    {
        $hash = (string) HashComparator::computeHash($key);
        $elem = null;

        foreach ($this->hashTable->table[$hash] ?? [] as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }

    /**
     * @param TK $key
     * @param TV $value
     * @return self<TK, TV>
     * @psalm-suppress PropertyTypeCoercion
     */
    public function update(mixed $key, mixed $value): self
    {
        $hash = (string) HashComparator::computeHash($key);

        if (!isset($this->hashTable->table[$hash])) {
            $this->hashTable->table[$hash] = [];
        }

        $replacedIdx = -1;

        foreach ($this->hashTable->table[$hash] as $idx => [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $replacedIdx = $idx;
                $this->hashTable->table[$hash][$idx][1] = $value;
            }
        }

        if ($replacedIdx < 0) {
            $this->hashTable->table[$hash][] = [$key, $value];
        }

        return $this;
    }

    /**
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        return new HashMap($this->hashTable);
    }
}
