<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @extends NonEmptyCollection<TV>
 * @extends NonEmptySetOps<TV>
 * @extends NonEmptySetCasts<TV>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps, NonEmptySetCasts
{

}
