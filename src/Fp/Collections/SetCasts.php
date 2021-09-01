<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SetCasts
{
    /**
     * @return list<TV>
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
     * @return HashSet<TV>
     */
    public function toHashSet(): HashSet;
}
