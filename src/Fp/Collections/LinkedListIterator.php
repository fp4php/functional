<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

class LinkedListIterator implements Iterator
{
    private LinkedList $originalList;
    private LinkedList $list;
    private int $idx;

    public function __construct(LinkedList $list)
    {
        $this->originalList = $this->list = $list;
        $this->idx = 0;
    }

    public function current(): mixed
    {
        return $this->list instanceof Cons
            ? $this->list->head
            : null;
    }

    public function next(): void
    {
        if ($this->list instanceof Cons) {
            $this->list = $this->list->tail;
            $this->idx++;
        }
    }

    public function key(): int
    {
        return $this->idx;
    }

    public function valid(): bool
    {
        return $this->list instanceof Cons;
    }

    public function rewind(): void
    {
        $this->list = $this->originalList;
        $this->idx = 0;
    }
}
