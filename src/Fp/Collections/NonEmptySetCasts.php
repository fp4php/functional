<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySetCasts
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
     * @return ArrayList<TV>
     */
    public function toArrayList(): ArrayList;

    /**
     * @return NonEmptyLinkedList<TV>
     */
    public function toNonEmptyLinkedList(): NonEmptyLinkedList;

    /**
     * @return NonEmptyArrayList<TV>
     */
    public function toNonEmptyArrayList(): NonEmptyArrayList;

    /**
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;

    /**
     * @return NonEmptyHashSet<TV>
     */
    public function toNonEmptyHashSet(): NonEmptyHashSet;
}