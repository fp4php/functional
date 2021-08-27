<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @internal
 * @template TV
 * @implements Iterator<int, TV>
 */
final class LinkedListIterator implements Iterator
{
    private LinkedList $originalList;
    private LinkedList $list;
    private int $idx;

    /**
     * @param LinkedList<TV> $list
     */
    public function __construct(LinkedList $list)
    {
        $this->originalList = $this->list = $list;
        $this->idx = 0;
    }

    public function current(): mixed
    {
        return $this->list->value instanceof Cons
            ? $this->list->value->head
            : null;
    }

    public function next(): void
    {
        if ($this->list->value instanceof Cons) {
            $this->list = $this->list->value->tail;
            $this->idx++;
        }
    }

    public function key(): int
    {
        return $this->idx;
    }

    public function valid(): bool
    {
        return $this->list->value instanceof Cons;
    }

    public function rewind(): void
    {
        $this->list = $this->originalList;
        $this->idx = 0;
    }
}
