<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TV
 * @extends NonEmptyCollection<TV>
 * @extends NonEmptySetOps<TV>
 * @extends NonEmptySetCollector<TV>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps, NonEmptySetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
