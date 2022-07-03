<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TV
 * @extends NonEmptySeqChainableOps<TV>
 * @extends NonEmptySeqTerminalOps<TV>
 * @extends NonEmptySeqCastableOps<TV>
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface NonEmptySeqOps extends NonEmptySeqChainableOps, NonEmptySeqTerminalOps, NonEmptySeqCastableOps
{

}
