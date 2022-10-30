<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TV
 * @extends NonEmptyCollection<int, TV>
 * @extends NonEmptySetOps<TV>
 * @extends NonEmptySetCollector<TV>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps, NonEmptySetCollector
{
    /**
     * {@inheritDoc}
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator;
}
