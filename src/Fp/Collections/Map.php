<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template TK
 * @template-covariant TV
 * @extends Collection<array{TK, TV}>
 * @extends MapOps<TK, TV>
 * @extends MapCollector<TK, TV>
 */
interface Map extends Collection, MapOps, MapCollector
{
    /**
     * {@inheritDoc}
     * @return Iterator<array{TK, TV}>
     */
    public function getIterator(): Iterator;
}
