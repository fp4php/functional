<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements StreamChainableOps<TV>
 * @implements StreamTerminalOps<TV>
 * @implements StreamCastableOps<TV>
 */
interface StreamOps extends StreamChainableOps, StreamTerminalOps, StreamCastableOps
{

}
