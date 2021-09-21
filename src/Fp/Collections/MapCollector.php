<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template TK
 * @template-covariant TV
 */
interface MapCollector
{
    /**
     * ```php
     * >>> HashMap::collect(['a' =>  1, 'b' => 2]);
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @param iterable<TKI, TVI> $source
     * @return self<TKI, TVI>
     */
    public static function collect(iterable $source): self;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]]);
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     * @param iterable<array{TKI, TVI}> $source
     * @return self<TKI, TVI>
     */
    public static function collectPairs(iterable $source): self;
}
