<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TV
 * @extends NonEmptyCollection<int, TV>
 * @extends NonEmptySeqOps<TV>
 * @extends NonEmptySeqCollector<TV>
 */
interface NonEmptySeq extends NonEmptyCollection, NonEmptySeqOps, NonEmptySeqCollector
{
    /**
     * {@inheritDoc}
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator;
}
