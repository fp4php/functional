<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySetChainableOps<TV>
 * @extends NonEmptySetTerminalOps<TV>
 * @extends NonEmptySetCastableOps<TV>
 */
interface NonEmptySetOps extends NonEmptySetChainableOps, NonEmptySetTerminalOps, NonEmptySetCastableOps
{

}
