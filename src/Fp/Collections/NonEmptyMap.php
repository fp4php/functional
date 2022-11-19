<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TK
 * @template-covariant TV
 * @extends NonEmptyCollection<TK, TV>
 * @extends NonEmptyMapOps<TK, TV>
 * @extends NonEmptyMapCollector<TK, TV>
 */
interface NonEmptyMap extends NonEmptyCollection, NonEmptyMapOps, NonEmptyMapCollector
{
}
