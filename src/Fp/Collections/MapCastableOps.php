<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template TK
 * @template-covariant TV
 */
interface MapCastableOps
{
    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArray();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return list<array{TK, TV}>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a',  1], ['b', 2]])->toAssocArray();
     * => ['a' => 1, 'b' => 2]
     * ```
     * @psalm-return (TK is array-key ? array<TK, TV> : never)
     */
    public function toAssocArray(): array;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toLinkedList();
     * => LinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toArrayList();
     * => ArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashSet();
     * => HashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap;
}
