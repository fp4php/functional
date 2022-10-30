<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends NonEmptyCollection<TK, TV>
 * @extends NonEmptyMapOps<TK, TV>
 * @extends NonEmptyMapCollector<TK, TV>
 */
interface NonEmptyMap extends NonEmptyCollection, NonEmptyMapOps, NonEmptyMapCollector
{
    /**
     * {@inheritDoc}
     * @return Iterator<TK, TV>
     */
    public function getIterator(): Iterator;
}
