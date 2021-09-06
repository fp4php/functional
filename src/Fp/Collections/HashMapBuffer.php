<?php

declare(strict_types=1);

namespace Fp\Collections;

use Error;
use Fp\Functional\Option\Option;

/**
 * Internal buffer
 * Which provides fast update operation
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

    private bool $closed = false;
    private bool $empty = true;

    public function __construct()
    {
        $this->hashTable = new HashTable();
    }

    /**
     * @param TK $key
     */
    public function get(mixed $key): Option
    {
        $this->assertIsOpen();

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
        $this->assertIsOpen();

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

        $this->empty = false;
        return $this;
    }

    /**
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap
    {
        $this->assertIsOpen();

        $this->closed = true;

        return new HashMap($this->hashTable);
    }

    public function isEmpty():bool
    {
        return $this->empty;
    }

    private function assertIsOpen(): void
    {
        if ($this->closed) {
            throw new Error(self::class . ' already closed');
        }
    }
}
