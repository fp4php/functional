<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template-covariant TV
 */
interface NonEmptySetCollector
{
    /**
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return Option<NonEmptySet<TVI>>
     */
    public static function collect(iterable $source): Option;

    /**
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return NonEmptySet<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptySet;

    /**
     * @template TVI
     *
     * @param (non-empty-array<array-key, TVI>|NonEmptyCollection<mixed, TVI>) $source
     * @return NonEmptySet<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptySet;

    /**
     * ```php
     * >>> NonEmptyHashSet::singleton(1)->toList();
     * => [1]
     * ```
     *
     * @template TVI
     *
     * @param TVI $val
     * @return NonEmptySet<TVI>
     */
    public static function singleton(mixed $val): NonEmptySet;
}
