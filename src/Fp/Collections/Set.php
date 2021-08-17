<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV of (object|scalar)
 * @extends Collection<TV>
 * @extends SetOps<TV>
 */
interface Set extends Collection, SetOps
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
     * REPL:
     * >>> HashSet::collect([1, 2])
     * => HashSet(1, 2)
     *
     * @psalm-pure
     * @template TVI of (object|scalar)
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;
}
