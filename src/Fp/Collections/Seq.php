<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends Collection<TV>
 * @extends SeqOps<TV>
 * @extends SeqCastOps<TV>
 * @extends SeqCollector<TV>
 */
interface Seq extends Collection, SeqOps, SeqCastOps, SeqCollector
{
    /**
     * @inheritDoc
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
