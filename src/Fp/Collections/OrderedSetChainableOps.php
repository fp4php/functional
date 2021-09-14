<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface OrderedSetChainableOps
{
    /**
     * Returns every collection element except first
     *
     * REPL:
     * >>> HashSet::collect([1, 2, 3])->tail()->toArray()
     * => [2, 3]
     *
     * @psalm-return Set<TV>
     */
    public function tail(): Set;
}
