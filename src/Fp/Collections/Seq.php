<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * Ordered list of elements
 *
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 * @extends Collection<TV>
 * @extends SeqOps<TV>
 * @extends SeqCollector<TV>
 */
interface Seq extends Collection, SeqOps, SeqCollector
{
    /**
     * {@inheritDoc}
     *
     * @return Iterator<TV>
     */
    public function getIterator(): Iterator;
}
