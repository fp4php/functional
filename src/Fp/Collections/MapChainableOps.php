<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-suppress InvalidTemplateParam
 */
interface MapChainableOps
{
    /**
     * Produces new collection with given element
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->updated('b', 3)->toList();
     * => [['a', 1], ['b', 3]]
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param TKI $key
     * @param TVI $value
     * @return Map<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): Map;

    /**
     * Produces new collection without an element with given key
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->removed('b')->toList();
     * => [['a', 1]]
     * ```
     *
     * @param TK $key
     * @return Map<TK, TV>
     */
    public function removed(mixed $key): Map;

    /**
     * Filter collection by condition
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->filter(fn($e) => $e > 1)->toList();
     * => [['b', 2]]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Map<TK, TV>
     */
    public function filter(callable $predicate): Map;

    /**
     * Same as {@see MapChainableOps::filter()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     * @return Map<TK, TV>
     */
    public function filterKV(callable $predicate): Map;

    /**
     * A combined {@see MapOps::map} and {@see MapOps::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 'zero'], ['b', '1'], ['c', '2']])
     * >>>     ->filterMap(fn($e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
     * >>>     ->toList();
     * => [['b', 1], ['c', 2]]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Map<TK, TVO>
     */
    public function filterMap(callable $callback): Map;

    /**
     * Map collection and flatten the result
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['2', 2], ['5', 5]]);
     * => HashMap('2' -> 2, '5' -> 5)
     *
     * >>> $collection
     * >>>     ->flatMap(fn(int $val) => [
     * >>>         [$val - 1, $val - 1],
     * >>>         [$val, $val],
     * >>>         [$val + 1, $val + 1]
     * >>>     ])
     * >>>     ->toList();
     * => [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]]
     * ```
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): (iterable<array{TKO, TVO}>) $callback
     * @return Map<TKO, TVO>
     */
    public function flatMap(callable $callback): Map;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->map(fn($elem) => $elem + 1);
     * => HashMap('1' -> 2, '2' -> 3)
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return Map<TK, TVO>
     */
    public function map(callable $callback): Map;

    /**
     * Same as {@see MapChainableOps::map()}, but passing also the key to the $callback function.
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->mapKV(fn($key, $val) => "{$key}-{$val}");
     * => HashMap('1' -> '1-1', '2' -> '2-2')
     * ```
     *
     * @template TVO
     *
     * @param callable(TK, TV): TVO $callback
     * @return Map<TK, TVO>
     */
    public function mapKV(callable $callback): Map;

    /**
     * Produces a new collection of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->reindex(fn($v) => $v + 1);
     * => HashMap(2 -> 1, 3 -> 2)
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, TV>
     */
    public function reindex(callable $callback): Map;

    /**
     * Same as {@see MapChainableOps::reindex()}, but passing also the key to the $callback function.
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->reindexKV(fn($k, $v) => "{$k}-{$v}");
     * => HashMap('1-1' -> 1, '2-2' -> 2)
     * ```
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return Map<TKO, TV>
     */
    public function reindexKV(callable $callback): Map;

    /**
     * Returns sequence of collection keys
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->keys(fn($elem) => $elem + 1)->toList();
     * => ['1', '2']
     * ```
     *
     * @return Seq<TK>
     */
    public function keys(): Seq;

    /**
     * Returns sequence of collection values
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->values(fn($elem) => $elem + 1)->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function values(): Seq;
}
