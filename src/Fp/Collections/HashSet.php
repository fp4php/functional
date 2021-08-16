<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TV of (object|scalar)
 * @psalm-immutable
 * @implements Set<TV>
 */
class HashSet implements Set
{
    /**
     * @param Map<TV, TV> $map
     */
    private function __construct(private Map $map)
    {
    }

    /**
     * @return list<TV>
     */
    public function toArray(): array
    {
        return $this->toLinkedList()->toArray();
    }

    /**
     * @return LinkedList<TV>
     */
    public function toLinkedList(): LinkedList
    {
        return $this->map->toLinkedList()->map(fn($pair) => $pair[1]);
    }

    /**
     * @psalm-pure
     * @template TVI of (object|scalar)
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self
    {
        $pairs = LinkedList::collect($source)->map(fn(mixed $elem) => [$elem, $elem]);

        /** @var self<TVI> */
        return new self(HashMap::collect($pairs));
    }

    /**
     * @inheritDoc
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator
    {
        return new LinkedListIterator($this->toLinkedList());
    }
}
