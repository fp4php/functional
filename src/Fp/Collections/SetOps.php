<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends SetChainableOps<TV>
 * @extends SetUnchainableOps<TV>
 * @extends SetCastableOps<TV>
 */
interface SetOps extends SetChainableOps, SetUnchainableOps, SetCastableOps
{

}
