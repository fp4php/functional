<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * Ordered list of elements
 *
 * @template-covariant TV
 * @extends Collection<int, TV>
 * @extends SeqOps<TV>
 * @extends SeqCollector<TV>
 */
interface Seq extends Collection, SeqOps, SeqCollector
{
    /**
     * {@inheritDoc}
     *
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator;
}
