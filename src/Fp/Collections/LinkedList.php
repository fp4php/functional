<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * O(1) {@see Seq::prepended} operation
 * Fast {@see Seq::reverse} operation
 *
 * @psalm-immutable
 * @template-covariant TV
 * @extends AbstractSeq<TV>
 */
abstract class LinkedList extends AbstractSeq
{
    /**
     * @use SeqChainable<TV>
     */
    use SeqChainable;

    /**
     * @inheritDoc
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $buffer = new LinkedListBuffer();

        foreach ($source as $elem) {
            $buffer->append($elem);
        }

        return $buffer->toLinkedList();
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this);
    }

    /**
     * @inheritDoc
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return self<TV|TVI>
     */
    public function prepended(mixed $elem): self
    {
        return new Cons($elem, $this);
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function reverse(): self
    {
        $list = Nil::getInstance();

        foreach ($this as $elem) {
            $list = $list->prepended($elem);
        }

        return $list;
    }

    /**
     * @inheritDoc
     * @psalm-return self<TV>
     */
    public function tail(): self
    {
        return match (true) {
            $this instanceof Cons => $this->tail,
            $this instanceof Nil => $this,
        };
    }
}
