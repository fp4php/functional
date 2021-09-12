<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @implements StreamChainableOps<TV>
 * @implements StreamUnchainableOps<TV>
 * @implements StreamCastOps<TV>
 */
interface StreamOps extends StreamChainableOps, StreamUnchainableOps, StreamCastOps
{

}
