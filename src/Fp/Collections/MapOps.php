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
     * >>> HashMap::collect([['a', 1], ['b', 2]])->every(fn($elem) => $elem > 0)
     * => true
     * >>> HashMap::collect([['a', 1], ['b', 2]])->every(fn($elem) => $elem > 1)
     * => false
     *
     * @psalm-param callable(TV, TK): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Filter collection by condition
     *
     * Predicate argument order is reversed. It's value, then key.
     * The reason is the most cases do not use key at all.
     * And you can omit this key from your closure params.
     *
     * REPL:
     * >>> HashMap::collect([['a', 1], ['b', 2]])->filter(fn($elem) => $elem > 1)->toArray()
     * => [['b', 2]]
     *
     * @psalm-param callable(TV, TK): bool $predicate
     * @psalm-return Map<TK, TV>
     */
    public function filter(callable $predicate): Map;

    /**
     * Map collection and flatten the result
     *
     * Callback argument order is reversed. It's value, then key.
     * The reason is the most cases do not use key at all.
     * And you can omit this key from your closure params.
     *
     * REPL:
     * >>> $collection = HashMap::collect([['2', 2], ['5', 5]])
     * => HashMap('2' -> 2, '5' -> 5)
     * >>> $collection
     * >>>     ->flatMap(fn($e) => [[$e - 1, $e - 1], [$e, $e], [$e + 1, $e + 1]])
     * >>>     ->toArray()
     * => [[1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6]]
     *
     * @experimental
     * @psalm-template TKO
     * @psalm-template TVO
     * @psalm-param callable(TV, TK): iterable<array{TKO, TVO}> $callback
     * @psalm-return Map<TKO, TVO>
     */
    public function flatMap(callable $callback): Map;

    /**
     * Fold many pairs of key-value into one
     *
     * REPL:
     * >>> $collection = HashMap::collect([['2', 2], ['3', 3]])
     * => HashMap('2' -> 2, '3' -> 3)
     * >>> $collection->fold(['1', 1], fn(array $acc, array $cur): array => [$acc[0] . $cur[0], $acc[1] + $cur[1]])
     * => ['123', 6]
     *
     * @template TVI
     * @psalm-param TVI $init initial accumulator value
     * @psalm-param callable(TVI, array{TK, TV}): TVI $callback (accumulator, current element): new accumulator
     * @psalm-return TVI
     */
    public function fold(mixed $init, callable $callback): mixed;

    /**
     * Reduce multiple elements into one
     * Returns None for empty collection
     *
     * REPL:
     * >>> $collection = HashMap::collect([['2', 2], ['3', 3]])
     * => HashMap('2' -> 2, '3' -> 3)
     * >>> $collection
     * >>>     ->reduce(fn(array $acc, array $cur): array => [$acc[0] . $cur[0], $acc[1] + $cur[1]])
     * >>>     ->get()
     * => ['23', 5]
     *
     * @exprimental
     * @psalm-param callable(array{TK, TV}, array{TK, TV}): array{TK, TV} $callback (accumulator, current value): new accumulator
     * @psalm-return Option<array{TK, TV}>
     */
    public function reduce(callable $callback): Option;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * Callback argument order is reversed. It's value, then key.
     * The reason is the most cases do not use key at all.
     * And you can omit this key from your closure params.
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->map(fn($elem) => $elem + 1)
     * => HashMap('1' -> 2, '2' -> 3)
     *
     * @template TVO
     * @psalm-param callable(TV, TK): TVO $callback
     * @psalm-return Map<TK, TVO>
     */
    public function map(callable $callback): Map;

    /**
     * Produces a new collection of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * Callback argument order is reversed. It's value, then key.
     * The reason is the most cases do not use key at all.
     * And you can omit this key from your closure params.
     *
     * REPL:
     * >>> $collection = HashMap::collect([['1', 1], ['2', 2]])
     * => HashMap('1' -> 1, '2' -> 2)
     * >>> $collection->reindex(fn($elem) => $elem + 1)
     * => HashMap(2 -> 1, 3 -> 2)
     *
     * @template TKO
     * @psalm-param callable(TV, TK): TKO $callback
     * @psalm-return Map<TKO, TV>
     */
    public function reindex(callable $callback): Map;


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
