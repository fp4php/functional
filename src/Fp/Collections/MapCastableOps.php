<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Streams\Stream;

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
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is Map<TKO, TVO>
     *
     * @return array<TKO, TVO>
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
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is Map<TKO, TVO>
     *
     * @return Option<non-empty-array<TKO, TVO>>
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

    /**
     * If each element of the collection is an associative array then call of this method will fold all elements to one associative array.
     *
     * ```php
     * >>> HashMap::collect(['f' => ['fst' => 1], 's' => ['snd' => 2], 't' => ['thr' => 3]])->toMergedArray()
     * => ['fst' => 1, 'snd' => 2, 'thr' => 3]
     * >>> HashMap::collect(['f' => [1, 2], 's' => [3, 4], 't' => [5, 6]])->toMergedArray()
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @template TArray of array<TKO, TVO>
     * @psalm-if-this-is Map<TK, TArray>
     *
     * @return array<TKO, TVO>
     * @psalm-return (TArray is list ? list<TVO> : array<TKO, TVO>)
     */
    public function toMergedArray(): array;

    /**
     * Non-empty version of {@see SetCastableOps::toMergedArray()}.
     *
     * ```php
     * >>> HashMap::collect(['f' => ['fst' => 1], 's' => ['snd' => 2], 't' => ['thr' => 3]])->toNonEmptyMergedArray()
     * => Some(['fst' => 1, 'snd' => 2, 'thr' => 3])
     * >>> HashMap::collect(['f' => [1, 2], 's' => [3, 4], 't' => [5, 6]])->toNonEmptyMergedArray()
     * => Some([1, 2, 3, 4, 5, 6])
     * >>> HashMap::collect([])->toNonEmptyMergedArray()
     * => None
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @template TArray of array<TKO, TVO>
     * @psalm-if-this-is Map<TK, TArray>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     * @psalm-return (TArray is list ? Option<non-empty-list<TVO>> : Option<non-empty-array<TKO, TVO>>)
     */
    public function toNonEmptyMergedArray(): Option;

    /**
     * ```php
     * >>> HashMap::collectPairs([['fst', 1], ['snd', 2], ['thr', 3]])
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
