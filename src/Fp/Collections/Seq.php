<?php

declare(strict_types=1);

namespace Fp\Collections;

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
}
