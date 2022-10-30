<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TV
 * @extends Collection<int, TV>
 * @extends SetOps<TV>
 * @extends SetCollector<TV>
 */
interface Set extends Collection, SetOps, SetCollector
{
}
