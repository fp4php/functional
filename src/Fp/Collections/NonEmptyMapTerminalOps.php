<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\FoldOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\FoldMethodReturnTypeProvider;

/**
 * @template TK
 * @template-covariant TV
 *
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
     * Fold many elements into one
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'snd' => 2, 'thr' => 3])->fold('0')(fn($acc, $cur) => $acc . $cur);
     * => '0123'
     * ```
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldOperation<TV, TVO>
     *
     * @see FoldMethodReturnTypeProvider
     */
    public function fold(mixed $init): FoldOperation;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->every(fn($value) => $value > 0);
     * => true
     *
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->every(fn($value) => $value > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Same as {@see NonEmptyMapTerminalOps::every()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function everyKV(callable $predicate): bool;

    /**
     * Returns true if some collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->exists(fn($value) => $value > 0);
     * => true
     *
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->exists(fn($value) => $value > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Same as {@see NonEmptyMapTerminalOps::exists()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function existsKV(callable $predicate): bool;

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

    /**
     * Same as {@see NonEmptyMapTerminalOps::traverseOption()}, but passing also the key to the $callback function.
     *
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $callback
     * @return Option<NonEmptyMap<TK, TVO>>
     */
    public function traverseOptionKV(callable $callback): Option;

    /**
     * Same as {@see NonEmptyMapTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(HashMap(0 -> 1, 1 -> 2, 2 -> 3))
     *
     * >>> NonEmptyHashMap::collectNonEmpty([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is NonEmptyMap<TK, Option<TVO>>
     *
     * @return Option<NonEmptyMap<TK, TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Produces new collection without an element with given key
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->removed('b')->toList();
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
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])
     * >>>     ->filter(fn(int $value) => $value > 1)
     * >>>     ->toList();
     * => [['b', 2]]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Map<TK, TV>
     *
     * @see CollectionFilterMethodReturnTypeProvider
     */
    public function filter(callable $predicate): Map;

    /**
     * Same as {@see NonEmptyMapChainableOps::filter()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     * @return Map<TK, TV>
     *
     * @see CollectionFilterMethodReturnTypeProvider
     */
    public function filterKV(callable $predicate): Map;

    /**
     * A combined {@see NonEmptyHashMap::map} and {@see NonEmptyHashMap::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 'zero'], ['b', '1'], ['c', '2']])
     * >>>     ->filterMap(fn($value) => is_numeric($value) ? Option::some((int) $value) : Option::none())
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
     * Same as {@see NonEmptyMapChainableOps::filterMap()}, but passing also the key to the $callback function.
     *
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $callback
     * @return Map<TK, TVO>
     */
    public function filterMapKV(callable $callback): Map;

    /**
     * Map collection and flatten the result
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairsNonEmpty([['2', 2], ['5', 5]]);
     * => NonEmptyHashMap('2' -> 2, '5' -> 5)
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
     * @param callable(TV): iterable<array{TKO, TVO}> $callback
     * @return Map<TKO, TVO>
     */
    public function flatMap(callable $callback): Map;

    /**
     * Same as {@see NonEmptyMapChainableOps::flatMap()}, but passing also the key to the $callback function.
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): iterable<array{TKO, TVO}> $callback
     * @return Map<TKO, TVO>
     */
    public function flatMapKV(callable $callback): Map;

    /**
     * Returns sequence of collection keys
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairsNonEmpty([['1', 1], ['2', 2]]);
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->keys(fn($elem) => $elem + 1)->toList();
     * => ['1', '2']
     * ```
     *
     * @return NonEmptySeq<TK>
     */
    public function keys(): NonEmptySeq;

    /**
     * Returns sequence of collection values
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairsNonEmpty([['1', 1], ['2', 2]]);
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->values(fn($elem) => $elem + 1)->toList();
     * => [1, 2]
     * ```
     *
     * @return NonEmptySeq<TV>
     */
    public function values(): NonEmptySeq;
}
