<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\FoldingOperation;
use function Fp\id;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface SeqTerminalOps
{
    /**
     * Find element by its index (Starts from zero).
     * Returns None if there is no such collection element.
     *
     * ```php
     * >>> ArrayList::collect([1, 2])(1)->get();
     * => 2
     * ```
     *
     * Alias for {@see Seq::at()}
     *
     * @return Option<TV>
     */
    public function __invoke(int $index): Option;

    /**
     * Find element by its index (Starts from zero)
     * Returns None if there is no such collection element
     *
     * ```php
     * >>> ArrayList::collect([1, 2])->at(1)->get();
     * => 2
     * ```
     *
     * @return Option<TV>
     */
    public function at(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * and false otherwise
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> LinkedList::collect([1, 2])->every(fn($elem) => $elem > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> LinkedList::collect([new Foo(1), new Bar(2)])->everyOf(Foo::class);
     * => false
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Suppose you have an ArrayList<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<ArrayList<TVO>>
     * i.e. an Some<ArrayList<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(ArrayList(1, 2, 3))
     *
     * >>> ArrayList::collect([0, 1, 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<Seq<TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * Same as {@see SeqTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> ArrayList::collect([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(ArrayList(1, 2, 3))
     *
     * >>> ArrayList::collect([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is Seq<Option<TVO>>
     *
     * @return Option<Seq<TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Group elements
     *
     * ```php
     * >>> LinkedList::collect([1, 1, 3])
     * >>>     ->groupBy(fn($e) => $e)
     * >>>     ->map(fn(Seq $e) => $e->toList())
     * >>>     ->toList();
     * => [[1, [1, 1]], [3, [3]]]
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, NonEmptySeq<TV>>
     */
    public function groupBy(callable $callback): Map;

    /**
     * ```php
     * >>> LinkedList::collect([
     * >>>     ['id' => 10, 'sum' => 10],
     * >>>     ['id' => 10, 'sum' => 15],
     * >>>     ['id' => 10, 'sum' => 20],
     * >>>     ['id' => 20, 'sum' => 10],
     * >>>     ['id' => 20, 'sum' => 15],
     * >>>     ['id' => 30, 'sum' => 20],
     * >>> ])->groupMap(
     * >>>     fn(array $a) => $a['id'],
     * >>>     fn(array $a) => $a['sum'] + 1,
     * >>> );
     * => HashMap(
     * =>   10 -> NonEmptyArrayList(21, 16, 11),
     * =>   20 -> NonEmptyArrayList(16, 11),
     * =>   30 -> NonEmptyArrayList(21),
     * => )
     * ```
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return Map<TKO, NonEmptySeq<TVO>>
     */
    public function groupMap(callable $group, callable $map): Map;

    /**
     * Partitions this Seq<TV> into a Map<TKO, TVO> according to a discriminator function $group.
     * All the values that have the same discriminator are then transformed by the $map and
     * then reduced into a single value with the $reduce.
     *
     *  * ```php
     * >>> ArrayList::collect([
     * >>>      ['id' => 10, 'val' => 10],
     * >>>      ['id' => 10, 'val' => 15],
     * >>>      ['id' => 10, 'val' => 20],
     * >>>      ['id' => 20, 'val' => 10],
     * >>>      ['id' => 20, 'val' => 15],
     * >>>      ['id' => 30, 'val' => 20],
     * >>> ])->groupMapReduce(
     * >>>     fn(array $a) => $a['id'],
     * >>>     fn(array $a) => $a['val'],
     * >>>     fn(int $old, int $new) => $old + $new,
     * >>> );
     * => HashMap([10 => 45, 20 => 25, 30 => 20])
     * ```
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @param callable(TVO, TVO): TVO $reduce
     *
     * @return Map<TKO, TVO>
     */
    public function groupMapReduce(callable $group, callable $map, callable $reduce): Map;

    /**
     * Produces a new Map of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * ```php
     * >>> $collection = ArrayList::collect([1, 2, 3]);
     * => ArrayList(1, 2, 3)
     *
     * >>> $collection->reindex(fn($v) => "key-{$v}");
     * => HashMap('key-1' -> 1, 'key-2' -> 2, 'key-3' -> 3)
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, TV>
     */
    public function reindex(callable $callback): Map;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> LinkedList::collect([1, 2])->exists(fn($elem) => 3 === $elem);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * ```php
     * >>> LinkedList::collect([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> LinkedList::collect([1, new Foo(2)])->existsOf(Bar::class);
     * => false
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> LinkedList::collect([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get();
     * => Foo(2)
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Find last element of given class
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Bar(1), new Foo(2)])->lastOf(Foo::class)->get();
     * => Foo(2)
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Option<TVO>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> LinkedList::collect(['1', '2'])->fold('0')(fn($acc, $cur) => $acc . $cur);
     * => '012'
     * ```
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldingOperation<TV, TVO>
     */
    public function fold(mixed $init): FoldingOperation;

    /**
     * Reduce multiple elements into one
     * Returns None for empty collection
     *
     * ```php
     * >>> LinkedList::collect(['1', '2'])->reduce(fn($acc, $cur) => $acc . $cur)->get();
     * => '12'
     * ```
     *
     * @template TA
     *
     * @param callable(TV|TA, TV): (TV|TA) $callback
     * @return Option<TV|TA>
     */
    public function reduce(callable $callback): Option;

    /**
     * Return first collection element
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->head()->get();
     * => 1
     * ```
     *
     * @return Option<TV>
     */
    public function head(): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> LinkedList::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first collection element
     * Alias for {@see SeqOps::head}
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->firstElement()->get();
     * => 1
     * ```
     *
     * @return Option<TV>
     */
    public function firstElement(): Option;

    /**
     * Returns last collection element
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->lastElement()->get();
     * => 2
     * ```
     *
     * @return Option<TV>
     */
    public function lastElement(): Option;

    /**
     * Check if collection has no elements
     *
     * ```php
     * >>> LinkedList::collect([])->isEmpty();
     * => true
     * ```
     */
    public function isEmpty(): bool;

    /**
     * Displays all elements of this collection in a string
     * using start, end, and separator strings.
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->mkString("(", ",", ")")
     * => '(1,2,3)'
     *
     * >>> LinkedList::collect([])->mkString("(", ",", ")")
     * => '()'
     * ```
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string;
}
