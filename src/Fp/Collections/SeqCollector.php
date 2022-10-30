<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template-covariant TV
 */
interface SeqCollector
{
    /**
     * ```php
     * >>> LinkedList::collect([1, 2]);
     * => LinkedList(1, 2)
     * ```
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $source
     * @return Seq<TVI>
     */
    public static function collect(iterable $source): Seq;

    /**
     * ```php
     * >>> LinkedList::singleton(1)->toList();
     * => [1]
     * ```
     *
     * @template TVI
     *
     * @param TVI $val
     * @return Seq<TVI>
     */
    public static function singleton(mixed $val): Seq;

    /**
     * ```php
     * >>> LinkedList::empty()->toList();
     * => []
     * ```
     *
     * @return Seq<empty>
     */
    public static function empty(): Seq;

    /**
     * Collect elements
     * from $start to $stopExclusive with step $by.
     *
     * ```php
     * >>> LinkedList::range(0, 10, 2)->toList();
     * => [0, 2, 4, 6, 8]
     *
     * >>> LinkedList::range(0, 3)->toList();
     * => [0, 1, 2]
     *
     * >>> LinkedList::range(0, 0)->toList();
     * => []
     * ```
     *
     * @param positive-int $by
     * @return Seq<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): Seq;
}
