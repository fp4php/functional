<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends NonEmptyMap<TK, TV>
 */
interface NonEmptyOrderedMap extends NonEmptyMap
{
    /**
     * @inheritDoc
     * @return Iterator<array{TK, TV}>
     */
    public function getIterator(): Iterator;
}
