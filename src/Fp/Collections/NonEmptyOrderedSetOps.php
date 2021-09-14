<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyOrderedSetChainableOps<TV>
 * @extends NonEmptyOrderedSetTerminalOps<TV>
 */
interface NonEmptyOrderedSetOps extends NonEmptyOrderedSetChainableOps, NonEmptyOrderedSetTerminalOps
{

}
