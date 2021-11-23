<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 */
interface NonEmptyMapTerminalOps
{
    /**
     * Get an element by its key
     * Alias for @see NonEmptyMapOps::get
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])('b')->getOrElse(0);
     * => 2
     *
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option;

    /**
     * Get an element by its key
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->get('b')->getOrElse(0);
     * => 2
     *
     * >>> NonEmptyHashMap::collectNonEmpty(['a' => 1, 'b' => 2])->get('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 0);
     * => true
     *
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 1);
     * => false
     * ```
     *
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * A combined {@see NonEmptyMap::map} and {@see NonEmptyMap::every}.
     *
     * Predicate satisfying is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairs(['a' => 1, 'b' => 2])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyHashMap('a' -> 1, 'b' -> 2))
     *
     * >>> NonEmptyHashMap::collectPairs(['a' => 0, 'b' => 1])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): Option<TVO> $callback
     * @psalm-return Option<NonEmptyMap<TK, TVO>>
     */
    public function everyMap(callable $callback): Option;
}
