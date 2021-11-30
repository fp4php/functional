<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
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
     * >>> LinkedList::singleton(1)->toArray();
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
     * >>> LinkedList::empty()->toArray();
     * => []
     * ```
     *
     * @return self<empty>
     */
    public static function empty(): self;
}
