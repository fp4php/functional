<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends Collection<TK, TV>
 * @extends MapOps<TK, TV>
 * @extends MapCollector<TK, TV>
 */
interface Map extends Collection, MapOps, MapCollector
{
}
