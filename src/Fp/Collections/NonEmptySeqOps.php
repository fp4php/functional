<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 * @extends NonEmptySeqChainableOps<TV>
 * @extends NonEmptySeqTerminalOps<TV>
 * @extends NonEmptySeqCastableOps<TV>
 */
interface NonEmptySeqOps extends NonEmptySeqChainableOps, NonEmptySeqTerminalOps, NonEmptySeqCastableOps
{

}
