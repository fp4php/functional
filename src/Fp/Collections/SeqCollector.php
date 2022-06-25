<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-suppress InvalidTemplateParam
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
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function collect(iterable $source): self;

    /**
     * ```php
     * >>> LinkedList::singleton(1)->toList();
     * => [1]
     * ```
     *
     * @template TVI
     * @param TVI $val
     * @return self<TVI>
     */
    public static function singleton(mixed $val): self;

    /**
     * ```php
     * >>> LinkedList::empty()->toList();
     * => []
     * ```
     *
     * @return self<empty>
     */
    public static function empty(): self;

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
     * @psalm-param positive-int $by
     * @psalm-return self<int>
     */
    public static function range(int $start, int $stopExclusive, int $by = 1): self;
}
