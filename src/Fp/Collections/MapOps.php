<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends MapChainableOps<TK, TV>
 * @extends MapTerminalOps<TK, TV>
 * @extends MapCastableOps<TK, TV>
 */
interface MapOps extends MapChainableOps, MapTerminalOps, MapCastableOps
{

}
