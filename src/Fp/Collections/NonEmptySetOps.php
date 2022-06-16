<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 * @extends NonEmptySetChainableOps<TV>
 * @extends NonEmptySetTerminalOps<TV>
 * @extends NonEmptySetCastableOps<TV>
 */
interface NonEmptySetOps extends NonEmptySetChainableOps, NonEmptySetTerminalOps, NonEmptySetCastableOps
{

}
