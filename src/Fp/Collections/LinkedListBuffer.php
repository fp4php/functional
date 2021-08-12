<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * Provides constant time append to list
 *
 * @template TV
 */
class LinkedListBuffer
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
        $this->first = new Nil();
        $this->last = null;
        $this->length = 0;
    }

    /**
     * @param TV $elem
     * @return self<TV>
     */
    public function append(mixed $elem): self
    {
        $last1 = new Cons($elem, new Nil());

        if (0 === $this->length) {
            $this->first = $last1;
        } elseif (isset($this->last)) {
            /** @psalm-suppress InaccessibleProperty */
            $this->last->tail = $last1;
        }

        $this->last = $last1;
        $this->length += 1;

        return $this;
    }

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this->first;
    }
}
