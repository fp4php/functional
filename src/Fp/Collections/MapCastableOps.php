<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 */
interface MapCastableOps
{
    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toList();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return list<array{TK, TV}>
     */
    public function toList(): array;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toNonEmptyList();
     * => Some([['a', 1], ['b', 2]])
     * >>> HashMap::collect([])->toNonEmptyList();
     * => None
     * ```
     *
     * @return Option<non-empty-list<array{TK, TV}>>
     */
    public function toNonEmptyList(): Option;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a',  1], ['b', 2]])->toArray();
     * => ['a' => 1, 'b' => 2]
     * ```
     *
     * @psalm-return (TK is array-key ? array<TK, TV> : never)
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a',  1], ['b', 2]])->toNonEmptyArray();
     * => Some(['a' => 1, 'b' => 2])
     * >>> HashMap::collectPairs([])->toNonEmptyArray();
     * => None
     * ```
     *
     * @psalm-return (TK is array-key ? Option<non-empty-array<TK, TV>> : never)
     */
    public function toNonEmptyArray(): Option;

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
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toNonEmptyLinkedList();
     * => Some(NonEmptyLinkedList(['a', 1], ['b', 2]))
     * >>> HashMap::collect([])->toNonEmptyLinkedList();
     * => None
     * ```
     *
     * @return Option<NonEmptyLinkedList<array{TK, TV}>>
     */
    public function toNonEmptyLinkedList(): Option;

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
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toNonEmptyArrayList();
     * => Some(NonEmptyArrayList(['a', 1], ['b', 2]))
     * >>> HashMap::collect([])->toNonEmptyArrayList();
     * => None
     * ```
     *
     * @return Option<NonEmptyArrayList<array{TK, TV}>>
     */
    public function toNonEmptyArrayList(): Option;

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
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toNonEmptyHashSet();
     * => Some(NonEmptyHashSet(['a', 1], ['b', 2]))
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toNonEmptyHashSet();
     * => None
     * ```
     *
     * @return Option<NonEmptyHashSet<array{TK, TV}>>
     */
    public function toNonEmptyHashSet(): Option;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap;

    /**
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return Option<NonEmptyHashMap<TK, TV>>
     */
    public function toNonEmptyHashMap(): Option;
}
