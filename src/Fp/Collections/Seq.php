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
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;
}
