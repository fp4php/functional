<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 * @extends Map<TK, TV>
 */
interface OrderedMap extends Map
{
    /**
     * @inheritDoc
     * @return Iterator<array{TK, TV}>
     */
    public function getIterator(): Iterator;
}
