<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptyOrderedSetChainableOps
{
    /**
     * Returns every collection element except first
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->tail()->toArray()
     * => [2, 3]
     *
     * @psalm-return OrderedSet<TV>
     */
    public function tail(): OrderedSet;
}
