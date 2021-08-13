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
        $this->first = Nil::getInstance();
        $this->last = null;
        $this->length = 0;
    }

    /**
     * @param TV $elem
     * @return self<TV>
     */
    public function append(mixed $elem): self
    {
        $appended = new Cons($elem, Nil::getInstance());

        if (0 === $this->length) {
            $this->first = $appended;
        } elseif (isset($this->last)) {
            /**
             * @dies-from-psalm-suppress
             * @psalm-suppress InaccessibleProperty
             */
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
        return $this->first;
    }
}
