<?php

declare(strict_types=1);

namespace Fp\Streams;

/**
 * @template-covariant TV
 * @extends StreamChainableOps<TV>
 * @extends StreamTerminalOps<TV>
 * @extends StreamCastableOps<TV>
 */
interface StreamOps extends StreamChainableOps, StreamTerminalOps, StreamCastableOps
{

}
