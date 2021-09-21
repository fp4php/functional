<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
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
     * @param iterable<TVI> $source
     * @return Option<self<TVI>>
     */
    public static function collect(iterable $source): Option;

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
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collectUnsafe(iterable $source): self;

    /**
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2]);
     * => NonEmptyArrayList(1, 2)
     * ```
     *
     * @template TVI
     * @param non-empty-array<TVI>|NonEmptyCollection<TVI> $source
     * @return self<TVI>
     */
    public static function collectNonEmpty(array|NonEmptyCollection $source): self;
}
