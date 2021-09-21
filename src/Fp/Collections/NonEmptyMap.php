<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends NonEmptyCollection<array{TK, TV}>
 * @extends NonEmptyMapOps<TK, TV>
 * @extends NonEmptyMapCollector<TK, TV>
 */
interface NonEmptyMap extends NonEmptyCollection, NonEmptyMapOps, NonEmptyMapCollector
{
    /**
     * @inheritDoc
     * @return Iterator<array{TK, TV}>
     */
    public function getIterator(): Iterator;
}
