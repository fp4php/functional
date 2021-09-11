<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyCollection<TV>
 * @extends NonEmptySeqOps<TV>
 * @extends NonEmptySeqCasts<TV>
 * @extends NonEmptySeqCollector<TV>
 */
interface NonEmptySeq extends NonEmptyCollection, NonEmptySeqOps, NonEmptySeqCasts, NonEmptySeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
