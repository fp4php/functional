<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template TK
 * @template-covariant TV
 */
interface MapCastOps
{
    /**
     * @return list<array{TK, TV}>
     */
    public function toArray(): array;

    /**
     * @return LinkedList<array{TK, TV}>
     */
    public function toLinkedList(): LinkedList;

    /**
     * @return ArrayList<array{TK, TV}>
     */
    public function toArrayList(): ArrayList;

    /**
     * @return HashSet<array{TK, TV}>
     */
    public function toHashSet(): HashSet;

    /**
     * @return HashMap<TK, TV>
     */
    public function toHashMap(): HashMap;
}
