<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Streams\Stream;

/**
 * @template TK
 * @template-covariant TV
 */
interface NonEmptyMapCastableOps
{
    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toList();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return list<array{TK, TV}>
     */
    public function toList(): array;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toList();
     * => [['a', 1], ['b', 2]]
     * ```
     *
     * @return non-empty-list<array{TK, TV}>
     */
    public function toNonEmptyList(): array;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a',  1], ['b', 2]])->toArray();
     * => ['a' => 1, 'b' => 2]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyMap<TKO, TVO>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a',  1], ['b', 2]])->toNonEmptyArray();
     * => Some(['a' => 1, 'b' => 2])
     * >>> HashMap::collectPairs([])->toNonEmptyArray();
     * => None
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyMap<TKO, TVO>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyArray(): array;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toLinkedList();
     * => LinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyLinkedList();
     * => NonEmptyLinkedList(['a', 1], ['b', 2])
     * ```
     *
     * @return NonEmptyLinkedList<array{TK, TV}>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toArrayList();
     * => ArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyArrayList();
     * => NonEmptyArrayList(['a', 1], ['b', 2])
     * ```
     *
     * @return NonEmptyArrayList<array{TK, TV}>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toHashSet();
     * => HashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyHashSet();
     * => NonEmptyHashSet(['a', 1], ['b', 2])
     * ```
     *
     * @return NonEmptyHashSet<array{TK, TV}>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toHashMap();
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->toNonEmptyHashMap();
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @return NonEmptyHashMap<TK, TV>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap;

    /**
     * If each element of the collection is an associative array then call of this method will fold all elements to one associative array.
     *
     * ```php
     * >>> HashMap::collect(['f' => ['fst' => 1], 's' => ['snd' => 2], 't' => ['thr' => 3]])->toMergedArray()
     * => ['fst' => 1, 'snd' => 2, 'thr' => 3]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyMap<TK, array<TKO, TVO>>
     *
     * @return array<TKO, TVO>
     */
    public function toMergedArray(): array;

    /**
     * Non-empty version of {@see NonEmptyMapCastableOps::toMergedArray()}.
     *
     * ```php
     * >>> HashMap::collect(['f' => ['fst' => 1], 's' => ['snd' => 2], 't' => ['thr' => 3]])->toNonEmptyMergedArray()
     * => ['fst' => 1, 'snd' => 2, 'thr' => 3]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptyMap<TK, array<TKO, TVO>>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyMergedArray(): array;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['fst', 1], ['snd', 2], ['thr', 3]])
     * >>>     ->toStream()
     * >>>     ->lines()
     * >>>     ->drain();
     * => Array([0] => fst, [1] => 1)
     * => Array([0] => snd, [1] => 2)
     * => Array([0] => thr, [1] => 3)
     * ```
     *
     * @return Stream<array{TK, TV}>
     */
    public function toStream(): Stream;
}
