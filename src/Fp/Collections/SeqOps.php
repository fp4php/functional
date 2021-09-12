<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends SeqChainableOps<TV>
 * @extends SeqUnchainableOps<TV>
 * @extends SeqCastOps<TV>
 */
interface SeqOps extends SeqChainableOps, SeqUnchainableOps, SeqCastOps
{

}
