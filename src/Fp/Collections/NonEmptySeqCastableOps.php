<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Streams\Stream;

/**
 * @template-covariant TV
 */
interface NonEmptySeqCastableOps
{
    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2])->toList();
     * => [1, 2]
     * ```
     *
     * @return list<TV>
     */
    public function toList(): array;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2])->toList();
     * => [1, 2]
     * ```
     *
     * @return non-empty-list<TV>
     */
    public function toNonEmptyList(): array;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([['fst', 1], ['snd', 2]])->toArray();
     * => ['fst' => 1, 'snd' => 2]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptySeq<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([['fst', 1], ['snd', 2]])->toNonEmptyArray();
     * => ['fst' => 1, 'snd' => 2]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is NonEmptySeq<array{TKO, TVO}>
     *
     * @return non-empty-array<TKO, TVO>
     */
    public function toNonEmptyArray(): array;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2])->toLinkedList();
     * => LinkedList(1, 2)
     * ```
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2])->toArrayList();
     * => ArrayList(1, 2)
     * ```
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2])->toNonEmptyLinkedList();
     * => NonEmptyLinkedList(1, 2)
     * ```
     *
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2])->toNonEmptyArrayList();
     * => NonEmptyArrayList(1, 2)
     * ```
     *
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 2])->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 2])->toNonEmptyHashSet();
     * => NonEmptyHashSet(1, 2)
     * ```
     *
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([['fst', 1], ['snd', 2]])->toHashMap();
     * => HashMap('fst' -> 1, 'snd' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptySeq<array{TKI, TVI}>
     *
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(): HashMap;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([['fst', 1], ['snd', 2]])->toNonEmptyHashMap();
     * => NonEmptyHashMap('fst' -> 1, 'snd' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @psalm-if-this-is NonEmptySeq<array{TKI, TVI}>
     *
     * @return NonEmptyHashMap<TKI, TVI>
     */
    public function toNonEmptyHashMap(): NonEmptyHashMap;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty(['fst', 'snd', 'thd'])
     * >>>     ->toStream()
     * >>>     ->lines()
     * >>>     ->drain();
     * => 'fst'
     * => 'snd'
     * => 'thd'
     * ```
     *
     * @return Stream<TV>
     */
    public function toStream(): Stream;
}
