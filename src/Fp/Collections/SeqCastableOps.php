<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface SeqCastableOps
{
    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->toArray();
     * => [1, 2]
     * ```
     *
     * @return list<TV>
     */
    public function toArray(): array;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->toNonEmptyArray();
     * => Some([1, 2])
     * >>> ArrayList::collect([])->toNonEmptyArray();
     * => None
     * ```
     *
     * @return Option<non-empty-list<TV>>
     */
    public function toNonEmptyArray(): Option;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->toLinkedList();
     * => LinkedList(1, 2)
     * ```
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->toNonEmptyLinkedList();
     * => Some(NonEmptyLinkedList(1, 2))
     * >>> ArrayList::collect([])->toNonEmptyLinkedList();
     * => None
     * ```
     *
     * @return Option<NonEmptyLinkedList<TV>>
     */
    public function toNonEmptyLinkedList(): Option;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])->toArrayList();
     * => ArrayList(1, 2)
     * ```
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2, 2])->toNonEmptyArrayList();
     * => Some(NonEmptyArrayList(1, 2, 2))
     * >>> ArrayList::collect([])->toNonEmptyArrayList();
     * => None
     * ```
     *
     * @return Option<NonEmptyArrayList<TV>>
     */
    public function toNonEmptyArrayList(): Option;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2, 2])->toHashSet();
     * => HashSet(1, 2)
     * ```
     *
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2, 2])->toNonEmptyHashSet();
     * => Some(NonEmptyHashSet(1, 2))
     * >>> ArrayList::collect([])->toNonEmptyHashSet();
     * => None
     * ```
     *
     * @return Option<NonEmptyHashSet<TV>>
     */
    public function toNonEmptyHashSet(): Option;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])
     * >>>     ->toHashMap(fn($elem) => [(string) $elem, $elem]);
     * => HashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap;

    /**
     * ```php
     * >>> ArrayList::collect([1, 2])
     * >>>     ->toNonEmptyHashMap(fn($elem) => [(string) $elem, $elem]);
     * => Some(HashMap('1' -> 1, '2' -> 2))
     * >>> ArrayList::collect([])
     * >>>     ->toNonEmptyHashMap(fn($elem) => [(string) $elem, $elem]);
     * => None
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param callable(TV): array{TKI, TVI} $callback
     * @return Option<NonEmptyHashMap<TKI, TVI>>
     */
    public function toNonEmptyHashMap(callable $callback): Option;
}
