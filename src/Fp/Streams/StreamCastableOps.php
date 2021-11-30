<?php

declare(strict_types=1);

namespace Fp\Streams;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
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
     * >>> Stream::emits([1, 2, 2])->toLinkedList();
     * => LinkedList(1, 2, 2)
     * ```
     *
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->toArrayList();
     * => ArrayList(1, 2, 2)
     * ```
     *
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * ```php
     * >>> Stream::emits([1, 2, 2])->toNonEmptyArrayList();
     * => Some(NonEmptyArrayList(1, 2, 2))
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
     * >>> Stream::emits([1, 2])
     * >>>    ->toHashMap(fn($elem) => [(string) $elem, $elem]);
     * => HashMap('1' -> 1, '2' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap;

    /**
     * @param string $path file path
     * @param bool $append append to an existing file
     */
    public function toFile(string $path, bool $append = false): void;
}
