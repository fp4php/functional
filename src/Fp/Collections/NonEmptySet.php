<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyCollection<TV>
 * @extends NonEmptySetOps<TV>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps
{
    /**
     * @return non-empty-list<TV>
     */
    public function toArray(): array;

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList;

    /**
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList;

    /**
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet;

    /**
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     * @throws EmptyCollectionException
     */
    public static function collect(iterable $source): self;

    /**
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collectUnsafe(iterable $source): self;

    /**
     * @psalm-pure
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptySet<TVI>|NonEmptySeq<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(iterable $source): self;
}
