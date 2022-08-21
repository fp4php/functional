<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template-covariant TV
 */
interface NonEmptySeqCollector
{
    /**
     * ```php
     * >>> NonEmptyArrayList::collect([1, 2]);
     * => Some(NonEmptyArrayList(1, 2))
     *
     * >>> NonEmptyArrayList::collect([]);
     * => None
     * ```
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return Option<NonEmptySeq<TVI>>
     */
    public static function collect(iterable $source): Option;

    /**
     * ```php
     * >>> NonEmptyArrayList::singleton(1);
     * => NonEmptyArrayList(1)
     * ```
     *
     * @template TVI
     *
     * @param TVI $val
     * @return NonEmptySeq<TVI>
     */
    public static function singleton(mixed $val): NonEmptySeq;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectUnsafe([1, 2]);
     * => NonEmptyArrayList(1, 2)
     *
     * >>> NonEmptyArrayList::collectUnsafe([]);
     * PHP Error: Trying to get value of None
     * ```
     *
     * @template TVI
     *
     * @param iterable<TVI> $source
     * @return NonEmptySeq<TVI>
     */
    public static function collectUnsafe(iterable $source): NonEmptySeq;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2]);
     * => NonEmptyArrayList(1, 2)
     * ```
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<TVI> $source
     * @return NonEmptySeq<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): NonEmptySeq;
}
