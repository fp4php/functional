<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamCasts
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

    /**
     * @template TKI
     * @template TVI
     * @param callable(TV): array{TKI, TVI} $callback
     * @return HashMap<TKI, TVI>
     */
    public function toHashMap(callable $callback): HashMap;
}
