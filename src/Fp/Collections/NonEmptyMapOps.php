<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 */
interface NonEmptyMapOps
{
    /**
     * Get an element by its key
     * Alias for @see NonEmptyMapOps::get
     *
     * REPL:
     * >>> NonEmptyHashMap::collectIterable(['a' => 1, 'b' => 2])('b')->getOrElse(0)
     * => 2
     * >>> NonEmptyHashMap::collectIterable(['a' => 1, 'b' => 2])('c')->getOrElse(0)
     * => 0
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option;

    /**
     * Get an element by its key
     *
     * REPL:
     * >>> NonEmptyHashMap::collectIterable(['a' => 1, 'b' => 2])->get('b')->getOrElse(0)
     * => 2
     * >>> NonEmptyHashMap::collectIterable(['a' => 1, 'b' => 2])->get('c')->getOrElse(0)
     * => 0
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option;

    /**
     * Produces new collection with given element
     *
     * REPL:
     * >>> NonEmptyHashMap::collect([['a', 1], ['b', 2]])->updated('b', 3)->toArray()
     * => [['a', 1], ['b', 3]]
     *
     * @template TKI
     * @template TVI
     * @param TKI $key
     * @param TVI $value
     * @return NonEmptyMap<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): NonEmptyMap;

    /**
     * Produces new collection without an element with given key
     *
     * REPL:
     * >>> NonEmptyHashMap::collect([['a', 1], ['b', 2]])->removed('b')->toArray()
     * => [['a', 1]]
     *
     * @param TK $key
     * @return Map<TK, TV>
     */
    public function removed(mixed $key): Map;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * REPL:
     * >>> NonEmptyHashMap::collect([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 0)
     * => true
     * >>> NonEmptyHashMap::collect([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 1)
     * => false
     *
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Filter collection by condition
     *
     * REPL:
     * >>> NonEmptyHashMap::collect([['a', 1], ['b', 2]])->filter(fn(Entry $e) => $e->value > 1)->toArray()
     * => [['b', 2]]
     *
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     * @psalm-return Map<TK, TV>
     */
    public function filter(callable $predicate): Map;

    /**
     * A combined {@see NonEmptyHashMap::map} and {@see NonEmptyHashMap::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * REPL:
     * >>> NonEmptyHashMap::collectNonEmpty([['a', 'zero'], ['b', '1'], ['c', '2']])
     * >>>     ->filterMap(fn(Entry $e) => is_numeric($e->value) ? Option::some((int) $e->value) : Option::none())
     * >>>     ->toArray()
     * => [['b', 1], ['c', 2]]
     *
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): Option<TVO> $callback
     * @psalm-return Map<TK, TVO>
     */
    public function filterMap(callable $callback): Map;

    /**
     * Alias for {@see NonEmptyMapOps::mapValues()}
     *
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> $collection = NonEmptyHashMap::collect([['1', 1], ['2', 2]])
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     * >>> $collection->map(fn(Entry $e) => $e->value + 1)
     * => NonEmptyHashMap('1' -> 2, '2' -> 3)
     *
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return NonEmptyMap<TK, TVO>
     */
    public function map(callable $callback): NonEmptyMap;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> $collection = NonEmptyHashMap::collect([['1', 1], ['2', 2]])
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     * >>> $collection->mapValues(fn(Entry $e) => $e->value + 1)
     * => NonEmptyHashMap('1' -> 2, '2' -> 3)
     *
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return NonEmptyMap<TK, TVO>
     */
    public function mapValues(callable $callback): NonEmptyMap;

    /**
     * Produces a new collection of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * REPL:
     * >>> $collection = NonEmptyHashMap::collect([['1', 1], ['2', 2]])
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     * >>> $collection->mapKeys(fn(Entry $e) => $e->value + 1)
     * => NonEmptyHashMap(2 -> 1, 3 -> 2)
     *
     * @template TKO
     * @psalm-param callable(Entry<TK, TV>): TKO $callback
     * @psalm-return NonEmptyMap<TKO, TV>
     */
    public function mapKeys(callable $callback): NonEmptyMap;

    /**
     * Returns sequence of collection keys
     *
     * REPL:
     * >>> $collection = NonEmptyHashMap::collect([['1', 1], ['2', 2]])
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     * >>> $collection->keys(fn($elem) => $elem + 1)->toArray()
     * => ['1', '2']
     *
     * @psalm-return NonEmptySeq<TK>
     */
    public function keys(): NonEmptySeq;

    /**
     * Returns sequence of collection values
     *
     * REPL:
     * >>> $collection = NonEmptyHashMap::collect([['1', 1], ['2', 2]])
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     * >>> $collection->values(fn($elem) => $elem + 1)->toArray()
     * => [1, 2]
     *
     * @psalm-return NonEmptySeq<TV>
     */
    public function values(): NonEmptySeq;
}