<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template-covariant TV
 * @extends Collection<TV>
 * @extends SetOps<TV>
 * @extends SetCollector<TV>
 */
interface Set extends Collection, SetOps, SetCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
