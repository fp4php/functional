<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * Provides constant time append to list
 *
 * @template TV
 */
final class LinkedListBuffer
{
    /**
     * @var LinkedList<TV>
     */
    private LinkedList $first;

    /**
     * @var null|Cons<TV>
     */
    private ?Cons $last;

    private int $length;

    public function __construct()
    {
        $this->flush();
    }

    /**
     * @param TV $elem
     * @return LinkedListBuffer<TV>
     */
    public function append(mixed $elem): LinkedListBuffer
    {
        $appended = new Cons($elem, Nil::getInstance());

        if (0 === $this->length) {
            $this->first = $appended;
        } elseif (isset($this->last)) {
            $this->last->tail = $appended;
        }

        $this->last = $appended;
        $this->length += 1;

        return $this;
    }

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        $first = $this->first;
        $this->flush();

        return $first;
    }

    private function flush(): void
    {
        $this->first = Nil::getInstance();
        $this->last = null;
        $this->length = 0;
    }
}
