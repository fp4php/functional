<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
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
     * >>> ArrayList::collect([1, 2])->toLinkedList();
     * => LinkedList(1, 2)
     * ```
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

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
     * >>> ArrayList::collect([1, 2])
     * >>>     ->toHashMap(fn($elem) => [(string) $elem, $elem]);
     * => HashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap;
}
