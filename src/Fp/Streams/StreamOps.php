<?php

declare(strict_types=1);

namespace Fp\Streams;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 * @implements StreamChainableOps<TV>
 * @implements StreamTerminalOps<TV>
 * @implements StreamCastableOps<TV>
 */
interface StreamOps extends StreamChainableOps, StreamTerminalOps, StreamCastableOps
{

}
