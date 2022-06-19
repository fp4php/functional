<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Generator;

/**
 * @internal
 *
 * @template TK
 * @template TV
 *
 * @psalm-type hash = string
 */
final class HashTable
{
    /**
     * @var array<hash, list<array{TK, TV}>>
     */
    public array $table = [];

    /**
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option
    {
        $hash = (string) HashComparator::computeHash($key);
        $elem = null;

        foreach ($this->table[$hash] ?? [] as [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $elem = $v;
            }
        }

        return Option::fromNullable($elem);
    }

    /**
     * @param TK $key
     * @param TV $value
     *
     * @psalm-suppress PropertyTypeCoercion
     */
    public function update(mixed $key, mixed $value): void
    {
        $hash = (string) HashComparator::computeHash($key);

        if (!isset($this->table[$hash])) {
            $this->table[$hash] = [];
        }

        $replacedPos = -1;

        foreach ($this->table[$hash] as $idx => [$k, $v]) {
            if (HashComparator::hashEquals($key, $k)) {
                $replacedPos = $idx;
                $this->table[$hash][$idx][1] = $value;
            }
        }

        if ($replacedPos < 0) {
            $this->table[$hash][] = [$key, $value];
        }
    }

    /**
     * @return Generator<int, array{TK, TV}>
     */
    public function getPairsGenerator(): Generator
    {
        foreach ($this->table as $bucket) {
            foreach ($bucket as $pair) {
                yield $pair;
            }
        }
    }

    /**
     * @return Generator<TK, TV>
     */
    public function getKeyValueIterator(): Generator
    {
        foreach ($this->table as $bucket) {
            foreach ($bucket as [$key, $value]) {
                yield $key => $value;
            }
        }
    }
}
