<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TV
 * @extends SeqChainableOps<TV>
 * @extends SeqTerminalOps<TV>
 * @extends SeqCastableOps<TV>
 */
interface SeqOps extends SeqChainableOps, SeqTerminalOps, SeqCastableOps
{

}
