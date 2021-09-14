<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySet<TV>
 * @extends NonEmptyOrderedSetOps<TV>
 */
interface NonEmptyOrderedSet extends NonEmptySet, NonEmptyOrderedSetOps
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
