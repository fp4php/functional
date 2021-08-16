<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
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
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;
}
