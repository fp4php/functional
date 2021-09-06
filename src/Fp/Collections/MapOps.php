<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-immutable
 */
interface MapOps
{
    /**
     * Get an element by its key
     * Alias for @see MapOps::get
     *
     * REPL:
     * >>> HashMap::collectIterable(['a' => 1, 'b' => 2])('b')->getOrElse(0)
     * => 2
     * >>> HashMap::collectIterable(['a' => 1, 'b' => 2])('c')->getOrElse(0)
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
     * >>> HashMap::collectIterable(['a' => 1, 'b' => 2])->get('b')->getOrElse(0)
     * => 2
     * >>> HashMap::collectIterable(['a' => 1, 'b' => 2])->get('c')->getOrElse(0)
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
     * >>> HashMap::collect([['a', 1], ['b', 2]])->updated('b', 3)->toArray()
     * => [['a', 1], ['b', 3]]
     *
     * @template TKI
     * @template TVI
     * @param TKI $key
     * @param TVI $value
     * @return Map<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): Map;

    /**
     * Produces new collection without an element with given key
     *
     * REPL:
     * >>> HashMap::collect([['a', 1], ['b', 2]])->removed('b')->toArray()
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
     * >>> HashMap::collect([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 0)
     * => true
     * >>> HashMap::collect([['a', 1], ['b', 2]])->every(fn($entry) => $entry->value > 1)
     * => false
     *
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Filter collection by condition
     *
     * REPL:
     * >>> HashMap::collect([['a', 1], ['b', 2]])->filter(fn(Entry $e) => $e->value > 1)->toArray()
     * => [['b', 2]]
     *
     * @psalm-param callable(Entry<TK, TV>): bool $predicate
     * @psalm-return Map<TK, TV>
     */
    public function filter(callable $predicate): Map;

    /**
     * A combined {@see MapOps::map} and {@see MapOps::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * REPL:
     * >>> HashMap::collect([['a', 'zero'], ['b', '1'], ['c', '2']])
     * >>>     ->filterMap(fn(Entry $e) => is_numeric($e) ? Option::some((int) $e) : Option::none())
     * >>>     ->toArray()
     * => [['b', 1], ['c', 2]]
     *
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): Option<TVO> $callback
     * @psalm-return Map<TK, TVO>
     */
    public function filterMap(callable $callback): Map;

    /**
     * Map collection and flatten the result
     *
     * REPL:
     * >>> $collection = HashMap::collect([['2', 2], ['5', 5]])
     * => HashMap('2' -> 2, '5' -> 5)
     * >>> $collection
     * >>>     ->flatMap(fn(Entry $e) => [
     * >>>         [$e->value - 1, $e->value - 1],
     * >>>         [$e->value, $e->value],
     * >>>         [$e->value + 1, $e->value + 1]
     * >>>     ])
     * >>>     ->toArray()
     * => [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]]
     *
     * @experimental
     * @psalm-template TKO
     * @psalm-template TVO
     * @psalm-param callable(Entry<TK, TV>): iterable<array{TKO, TVO}> $callback
     * @psalm-return Map<TKO, TVO>
     */
    public function flatMap(callable $callback): Map;

    /**
     * Fold many pairs of key-value into one
     *
     * REPL:
     * >>> $collection = HashMap::collect([['2', 2], ['3', 3]])
     * => HashMap('2' -> 2, '3' -> 3)
     * >>> $collection->fold(1, fn(int $acc, Entry $cur): int => $acc + $cur->value])
     * => 6
     *
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, Entry<TK, TV>): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed;

    /**
     * Alias for {@see MapOps::mapValues()}
     *
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->map(fn(Entry $e) => $e->value + 1)
     * => HashMap('1' -> 2, '2' -> 3)
     *
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return Map<TK, TVO>
     */
    public function map(callable $callback): Map;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->mapValues(fn(Entry $e) => $e->value + 1)
     * => HashMap('1' -> 2, '2' -> 3)
     *
     * @template TVO
     * @psalm-param callable(Entry<TK, TV>): TVO $callback
     * @psalm-return Map<TK, TVO>
     */
    public function mapValues(callable $callback): Map;

    /**
     * Produces a new collection of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->mapKeys(fn(Entry $e) => $e->value + 1)
     * => HashMap(2 -> 1, 3 -> 2)
     *
     * @template TKO
     * @psalm-param callable(Entry<TK, TV>): TKO $callback
     * @psalm-return Map<TKO, TV>
     */
    public function mapKeys(callable $callback): Map;

    /**
     * Returns sequence of collection keys
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->keys(fn($elem) => $elem + 1)->toArray()
     * => ['1', '2']
     *
     * @psalm-return Seq<TK>
     */
    public function keys(): Seq;

    /**
     * Returns sequence of collection values
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->values(fn($elem) => $elem + 1)->toArray()
     * => [1, 2]
     *
     * @psalm-return Seq<TV>
     */
    public function values(): Seq;
}
