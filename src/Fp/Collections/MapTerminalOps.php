<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 */
interface MapTerminalOps
{
    /**
     * Get an element by its key
     * Alias for {@see MapOps::get}
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('c')->getOrElse(0);
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
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('c')->getOrElse(0);
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
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 0);
     * => true
     *
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 1);
     * => false
     * ```
     *
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * A combined {@see Map::map} and {@see Map::every}.
     *
     * Predicate satisfying is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> HashMap::collectPairs(['a' => 1, 'b' => 2])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(HashMap('a' -> 1, 'b' -> 2))
     *
     * >>> HashMap::collectPairs(['a' => 0, 'b' => 1])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): Option<TVO> $callback
     * @psalm-return Option<Map<TK, TVO>>
     */
    public function everyMap(callable $callback): Option;

    /**
     * Fold many pairs of key-value into one
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['2', 2], ['3', 3]]);
     * => HashMap('2' -> 2, '3' -> 3)
     *
     * >>> $collection->fold(1, fn(int $acc, Entry $cur): int => $acc + $cur->value]);
     * => 6
     * ```
     *
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, Entry<TK, TV>): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed;
}
