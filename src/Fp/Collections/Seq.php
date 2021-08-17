<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends Collection<TV>
 * @extends SeqOps<TV>
 */
interface Seq extends Collection, SeqOps
{
    /**
     * @return list<TV>
     */
    public function toArray(): array;

    /**
     * REPL:
     * >>> LinkedList::collect([1, 2])
     * => LinkedList(1, 2)
     *
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;
}
