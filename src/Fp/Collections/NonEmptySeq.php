<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyCollection<empty, TV>
 * @extends NonEmptySeqOps<TV>
 */
interface NonEmptySeq extends NonEmptyCollection, NonEmptySeqOps
{
    /**
     * @psalm-pure
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;
}
