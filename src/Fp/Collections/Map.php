<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TK
 * @template-covariant TV
 * @extends Collection<TK, TV>
 * @extends MapOps<TK, TV>
 */
interface Map extends Collection, MapOps
{

}
