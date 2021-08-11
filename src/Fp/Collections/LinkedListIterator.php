<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

class LinkedListIterator implements Iterator
{
    private LinkedList $originalList;
    private LinkedList $list;

    public function __construct(LinkedList $list)
    {
        $this->originalList = $this->list = $list;
    }

    public function current(): mixed
    {
        return $this->list instanceof Cons
            ? $this->list->head
            : null;
    }

    public function next(): void
    {
        $this->list = match (true) {
            $this->list instanceof Cons => $this->list->tail,
            $this->list instanceof Nil => $this->list,
        };
    }

    public function key()
    {
        return null;
    }

    public function valid(): bool
    {
        return $this->list instanceof Cons;
    }

    public function rewind(): void
    {
        $this->list = $this->originalList;
    }
}
