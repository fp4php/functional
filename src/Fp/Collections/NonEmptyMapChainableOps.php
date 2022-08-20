<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template-covariant TV
 * @psalm-suppress InvalidTemplateParam
 */
interface NonEmptyMapChainableOps
{
    /**
     * Produces new collection with given element
     *
     * ```php
     * >>> NonEmptyHashMap::collectPairsNonEmpty([['a', 1], ['b', 2]])->updated('b', 3)->toList();
     * => [['a', 1], ['b', 3]]
     * ```
     *
     * @template TKI
     * @template TVI
     *
     * @param TKI $key
     * @param TVI $value
     * @return NonEmptyMap<TK|TKI, TV|TVI>
     */
    public function updated(mixed $key, mixed $value): NonEmptyMap;

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
     */
    public function filter(callable $predicate): Map;

    /**
     * Same as {@see NonEmptyMapChainableOps::filter()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     * @return Map<TK, TV>
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
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairsNonEmpty([['1', 1], ['2', 2]]);
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->map(fn($elem) => $elem + 1);
     * => NonEmptyHashMap('1' -> 2, '2' -> 3)
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptyMap<TK, TVO>
     */
    public function map(callable $callback): NonEmptyMap;

    /**
     * Same as {@see NonEmptyMapChainableOps::map()}, but passing also the key to the $callback function.
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairsNonEmpty([['1', 1], ['2', 2]]);
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->mapKV(fn($key, $elem) => "{$key}-{$elem}");
     * => NonEmptyHashMap('1' -> '1-1', '2' -> '2-2')
     * ```
     *
     * @template TVO
     *
     * @param callable(TK, TV): TVO $callback
     * @return NonEmptyMap<TK, TVO>
     */
    public function mapKV(callable $callback): NonEmptyMap;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> NonEmptyHashMap::collectParisNonEmpty([['fst', new Foo(1)], ['snd', new Foo(2)]])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return NonEmptyMap<TK, TV>
     */
    public function tap(callable $callback): NonEmptyMap;

    /**
     * Same as {@see NonEmptyMapChainableOps::tap()}, but passing also the key to the $callback function.
     *
     * @param callable(TK, TV): void $callback
     * @return NonEmptyMap<TK, TV>
     */
    public function tapKV(callable $callback): NonEmptyMap;

    /**
     * Produces a new collection of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairsNonEmpty([['1', 1], ['2', 2]]);
     * => NonEmptyHashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->reindex(fn($v) => $v + 1);
     * => NonEmptyHashMap(2 -> 1, 3 -> 2)
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, TV>
     */
    public function reindex(callable $callback): NonEmptyMap;

    /**
     * Same as {@see NonEmptyMapChainableOps::reindex()}, but passing also the key to the $callback function.
     *
     * ```php
     * >>> $collection = NonEmptyHashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->reindexKV(fn($k, $v) => "{$k}-{$v}");
     * => NonEmptyHashMap('1-1' -> 1, '2-2' -> 2)
     * ```
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return NonEmptyMap<TKO, TV>
     */
    public function reindexKV(callable $callback): NonEmptyMap;

    /**
     * Group elements
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty(['fst' => 1, 'snd' => 2, 'thr' => 3])
     * >>>     ->groupBy(fn($i) => 0 === $i % 2 ? 'even' : 'odd')
     * => NonEmptyHashMap('odd' => NonEmptyHashMap('fst' => 1, 'trd' => 3), 'even' => NonEmptyHashMap('snd' => 2))
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, NonEmptyMap<TK, TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap;

    /**
     * Same as {@see NonEmptyMapChainableOps::groupBy()}, but passing also the key to the $callback function.
     *
     * @template TKO
     *
     * @param callable(TK, TV): TKO $callback
     * @return NonEmptyMap<TKO, NonEmptyMap<TK, TV>>
     */
    public function groupByKV(callable $callback): NonEmptyMap;

    /**
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty([
     * >>>     '10-1' => ['id' => 10, 'sum' => 10],
     * >>>     '10-2' => ['id' => 10, 'sum' => 15],
     * >>>     '10-3' => ['id' => 10, 'sum' => 20],
     * >>>     '20-1' => ['id' => 20, 'sum' => 10],
     * >>>     '20-2' => ['id' => 20, 'sum' => 15],
     * >>>     '30-1' => ['id' => 30, 'sum' => 20],
     * >>> ])->groupMap(
     * >>>     fn(array $a) => $a['id'],
     * >>>     fn(array $a) => $a['sum'] + 1,
     * >>> );
     * => NonEmptyHashMap(
     * =>   10 -> NonEmptyHashMap('10-3' => 21, '10-2' => 16, '10-1' => 11),
     * =>   20 -> NonEmptyHashMap('20-2' => 16, '20-1' => 11),
     * =>   30 -> NonEmptyHashMap('30-1' => 21),
     * => )
     * ```
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return NonEmptyMap<TKO, NonEmptyMap<TK, TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyMap;

    /**
     * Same as {@see NonEmptyMapChainableOps::groupMap()}, but passing also the key to the $group and $map function.
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): TKO $group
     * @param callable(TK, TV): TVO $map
     * @return NonEmptyMap<TKO, NonEmptyMap<TK, TVO>>
     */
    public function groupMapKV(callable $group, callable $map): NonEmptyMap;

    /**
     * Partitions this NonEmptyMap<TK, TV> into a NonEmptyMap<TKO, TVO> according to a discriminator function $group.
     * All the values that have the same discriminator are then transformed by the $map and
     * then reduced into a single value with the $reduce.
     *
     * ```php
     * >>> NonEmptyHashMap::collectNonEmpty([
     * >>>     '10-1' => ['id' => 10, 'sum' => 10],
     * >>>     '10-2' => ['id' => 10, 'sum' => 15],
     * >>>     '10-3' => ['id' => 10, 'sum' => 20],
     * >>>     '20-1' => ['id' => 20, 'sum' => 10],
     * >>>     '20-2' => ['id' => 20, 'sum' => 15],
     * >>>     '30-1' => ['id' => 30, 'sum' => 20],
     * >>> ])->groupMapReduce(
     * >>>     fn(array $a) => $a['id'],
     * >>>     fn(array $a) => $a['sum'],
     * >>>     fn(int $old, int $new) => $old + $new,
     * >>> );
     * => NonEmptyHashMap([10 => 45, 20 => 25, 30 => 20])
     * ```
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return NonEmptyMap<TKO, TVO>
     */
    public function groupMapReduce(callable $group, callable $map, callable $reduce): NonEmptyMap;

    /**
     * Same as {@see NonEmptyMapChainableOps::groupMapReduce()}, but passing also the key to the $group and $map function.
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): TKO $group
     * @param callable(TK, TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return NonEmptyMap<TKO, TVO>
     */
    public function groupMapReduceKV(callable $group, callable $map, callable $reduce): NonEmptyMap;

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
