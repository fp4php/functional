<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TV
 */
interface SetCollector
{
    /**
     * ```php
     * >>> HashSet::collect([1, 2]);
     * => HashSet(1, 2)
     * ```
     *
     * @template TVI
     *
     * @param (iterable<TVI>|Collection<TVI>) $source
     * @return Set<TVI>
     */
    public static function collect(iterable $source): Set;
}
