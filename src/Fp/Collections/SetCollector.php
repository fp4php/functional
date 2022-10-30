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
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return Set<TVI>
     */
    public static function collect(iterable $source): Set;

    /**
     * ```php
     * >>> HashSet::singleton(1)->toList();
     * => [1]
     * ```
     *
     * @template TVI
     *
     * @param TVI $val
     * @return Set<TVI>
     */
    public static function singleton(mixed $val): Set;

    /**
     * ```php
     * >>> HashSet::empty()->toList();
     * => []
     * ```
     *
     * @return Set<empty>
     */
    public static function empty(): Set;
}
