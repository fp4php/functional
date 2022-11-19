<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @template TK
 * @template-covariant TV
 */
interface MapCollector
{
    /**
     * ```php
     * >>> HashMap::empty()->toList();
     * => []
     * ```
     *
     * @return HashMap<empty, empty>
     */
    public static function empty(): HashMap;

    /**
     * ```php
     * >>> HashMap::collect(['a' =>  1, 'b' => 2]);
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param iterable<TKI, TVI> $source
     * @return Map<TKI, TVI>
     */
    public static function collect(iterable $source): Map;

    /**
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]]);
     * => HashMap('a' -> 1, 'b' -> 2)
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param iterable<array{TKI, TVI}> $source
     * @return Map<TKI, TVI>
     */
    public static function collectPairs(iterable $source): Map;
}
