<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TV
 * @extends Collection<int, TV>
 * @extends SetOps<TV>
 * @extends SetCollector<TV>
 */
interface Set extends Collection, SetOps, SetCollector
{
    /**
     * {@inheritDoc}
     *
     * @return Iterator<int, TV>
     */
    public function getIterator(): Iterator;
}
