<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TV
 * @extends NonEmptyCollection<int, TV>
 * @extends NonEmptySetOps<TV>
 * @extends NonEmptySetCollector<TV>
 */
interface NonEmptySet extends NonEmptyCollection, NonEmptySetOps, NonEmptySetCollector
{
}
