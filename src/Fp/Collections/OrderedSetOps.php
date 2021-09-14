<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends OrderedSetChainableOps<TV>
 * @extends OrderedSetTerminalOps<TV>
 */
interface OrderedSetOps extends OrderedSetChainableOps, OrderedSetTerminalOps
{

}
