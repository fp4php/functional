<?php

declare(strict_types=1);

namespace Fp\Streams;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface StreamCastableOps
{
    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->toArray();
     * => [1, 2, 2]
     * ```
     *
     * @return list<TV>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 3])->toNonEmptyArray();
     * => Some([1, 2, 3])
     * >>> Stream::emits([])->toNonEmptyArray();
     * => None
     * ```
     *
     * @return Option<non-empty-list<TV>>
     */
    public function toNonEmptyArray(): Option;

    /**
     * ```php
     * >>> Stream::emits([['fst', 1], ['snd', 2]])->toAssocArray();
     * => ['fst' => 1, 'snd' => 2]
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is Stream<array{TKO, TVO}>
     *
     * @return array<TKO, TVO>
     */
    public function toAssocArray(): array;

    /**
     * ```php
     * >>> Stream::emits([['fst', 1], ['snd', 2]])->toNonEmptyAssocArray();
     * => Some(['fst' => 1, 'snd' => 2])
     * >>> Stream::emits([])->toNonEmptyAssocArray();
     * => None
     * ```
     *
     * @template TKO of array-key
     * @template TVO
     * @psalm-if-this-is Stream<array{TKO, TVO}>
     *
     * @return Option<non-empty-array<TKO, TVO>>
     */
    public function toNonEmptyAssocArray(): Option;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 3])->toLinkedList();
     * => LinkedList(1, 2, 3)
     * ```
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 3])->toNonEmptyLinkedList();
     * => Some(NonEmptyLinkedList(1, 2, 3))
     * >>> Stream::emits([])->toNonEmptyLinkedList();
     * => None
     * ```
     *
     * @return Option<NonEmptyLinkedList<TV>>
     */
    public function toNonEmptyLinkedList(): Option;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 3])->toArrayList();
     * => ArrayList(1, 2, 3)
     * ```
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 3])->toNonEmptyArrayList();
     * => Some(NonEmptyArrayList(1, 2, 3))
     * >>> Stream::emits([])->toNonEmptyArrayList();
     * => None
     * ```
     *
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->toNonEmptyHashSet();
     * => Some(NonEmptyHashSet(1, 2))
     * >>> Stream::emits([])->toNonEmptyHashSet();
     * => None
     * ```
     *
     * @return Option<NonEmptyHashSet<TV>>
     */
    public function toNonEmptyHashSet(): Option;

    /**
     * ```php
     * >>> Stream::emits([['fst', 1], ['snd', 2]])->toHashMap();
     * => HashMap('fst' -> 1, 'snd' -> 2)
     * ```
     *
     * @template TKO
     * @template TVO
     * @psalm-if-this-is Stream<array{TKO, TVO}>
     *
     * @return HashMap<TKO, TVO>
     */
    public function toHashMap(): HashMap;

    /**
     * ```php
     * >>> Stream::emits([['fst', 1], ['snd', 2]])->toNonEmptyHashMap();
     * => Some(NonEmptyHashMap('fst' -> 1, 'snd' -> 2))
     * >>> Stream::emits([])->toNonEmptyHashMap();
     * => None
     * ```
     *
     * @template TKO
     * @template TVO
     * @psalm-if-this-is Stream<array{TKO, TVO}>
     *
     * @return Option<NonEmptyHashMap<TKO, TVO>>
     */
    public function toNonEmptyHashMap(): Option;

    /**
     * @param string $path file path
     * @param bool $append append to an existing file
     */
    public function toFile(string $path, bool $append = false): void;
}
