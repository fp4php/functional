<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-suppress InvalidTemplateParam
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
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Suppose you have an NonEmptyHashMap<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<NonEmptyHashMap<TVO>>
     * i.e. an Some<NonEmptyHashMap<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairs(['a' => 1, 'b' => 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyHashMap('a' -> 1, 'b' -> 2))
     *
     * >>> NonEmptyHashMap::collectPairs(['a' => 0, 'b' => 1])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptyMap<TK, TVO>>
     */
    public function traverseOption(callable $callback): Option;
}
