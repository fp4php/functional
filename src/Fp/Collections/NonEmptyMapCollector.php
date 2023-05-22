<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template-covariant TK
 * @template-covariant TV
 */
interface NonEmptyMapCollector
{
    /**
     * ```php
     * >>> NonEmptyHashMap::collect(['a' =>  1, 'b' => 2]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return Option<NonEmptyMap<TKI, TVI>>
     */
    public static function collect(iterable $source): Option;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectUnsafe(['a' =>  1, 'b' => 2]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return NonEmptyMap<TKI, TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptyMap;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' =>  1, 'b' => 2]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param non-empty-array<TKI, TVI> $source
     * @return NonEmptyMap<TKI, TVI>
     */
    public static function collectNonEmpty(array $source): NonEmptyMap;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairs([['a', 1], ['b', 2]]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param (iterable<mixed, array{TKI, TVI}>|Collection<mixed, array{TKI, TVI}>) $source
     * @return Option<NonEmptyMap<TKI, TVI>>
     */
    public static function collectPairs(iterable $source): Option;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsUnsafe([['a', 1], ['b', 2]]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param (iterable<mixed, array{TKI, TVI}>|Collection<mixed, array{TKI, TVI}>) $source
     * @return NonEmptyMap<TKI, TVI>
     */
    public static function collectPairsUnsafe(iterable $source): NonEmptyMap;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]]);
     * => NonEmptyHashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param non-empty-array<array-key, array{TKI, TVI}> | NonEmptyCollection<mixed, array{TKI, TVI}> $source
     * @return NonEmptyMap<TKI, TVI>
     */
    public static function collectPairsNonEmpty(array|NonEmptyCollection $source): NonEmptyMap;
}
