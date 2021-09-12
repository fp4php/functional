<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptySeqChainableOps<TV>
 * @extends NonEmptySeqUnchainableOps<TV>
 * @extends NonEmptySeqCastableOps<TV>
 */
interface NonEmptySeqOps extends NonEmptySeqChainableOps, NonEmptySeqUnchainableOps, NonEmptySeqCastableOps
{

}
