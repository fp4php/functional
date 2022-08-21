<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\FoldingOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\FoldMethodReturnTypeProvider;

/**
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface NonEmptySeqTerminalOps
{
    /**
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->at(1)->get();
     * => 2
     * ```
     *
     * @return Option<TV>
     */
    public function at(int $index): Option;

    /**
     * Alias for {@see NonEmptySeqTerminalOps::at()}
     *
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])(1)->get();
     * => 2
     * ```
     *
     * @return Option<TV>
     */
    public function __invoke(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->every(fn($elem) => $elem > 1);
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
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Bar(2)])->everyOf(Foo::class);
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
     * Suppose you have an NonEmptyArrayList<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<NonEmptyArrayList<TVO>>
     * i.e. an Some<NonEmptyArrayList<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> NonEmptyArrayList::collect([1, 2, 3])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyArrayList(1, 2, 3))
     *
     * >>> NonEmptyArrayList::collect([0, 1, 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptySeq<TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * Same as {@see NonEmptySeqTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(NonEmptyArrayList(1, 2, 3))
     *
     * >>> NonEmptyArrayList::collectNonEmpty([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is NonEmptySeq<Option<TVO>>
     *
     * @return Option<NonEmptySeq<TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Group elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 1, 3])
     * >>>     ->groupBy(fn($e) => $e)
     * >>>     ->map(fn(Seq $e) => $e->toList())
     * >>>     ->toList();
     * => [[1, [1, 1]], [3, [3]]]
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, NonEmptySeq<TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap;

    /**
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([
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
     * => NonEmptyMap(
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
     * @return NonEmptyMap<TKO, NonEmptySeq<TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyMap;

    /**
     * Partitions this NonEmptySeq<TV> into a NonEmptyMap<TKO, TVO> according to a discriminator function $group.
     * All the values that have the same discriminator are then transformed by the $map and
     * then reduced into a single value with the $reduce.
     *
     *  * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([
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
     * Produces a new NonEmptyMap of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * ```php
     * >>> $collection = NonEmptyArrayList::collectNonEmpty([1, 2, 3]);
     * => NonEmptyArrayList(1, 2, 3)
     *
     * >>> $collection->reindex(fn($v) => "key-{$v}");
     * => NonEmptyHashMap('key-1' -> 1, 'key-2' -> 2, 'key-3' -> 3)
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, TV>
     */
    public function reindex(callable $callback): NonEmptyMap;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->exists(fn($elem) => 3 === $elem);
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
     * >>> NonEmptyLinkedList::collectNonEmpty([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([1, new Foo(2)])->existsOf(Bar::class);
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
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
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
     * >>> NonEmptyLinkedList::collectNonEmpty([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get();
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
     * >>> NonEmptyLinkedList::collectNonEmpty([
     *     new Foo(1),
     *     new Bar(1),
     *     new Foo(2)
     * ])->lastOf(Foo::class)->get();
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
     * Return first collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->head();
     * => 1
     * ```
     *
     * @return TV
     */
    public function head(): mixed;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySeqOps::head}
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->firstElement();
     * => 1
     * ```
     *
     * @return TV
     */
    public function firstElement(): mixed;

    /**
     * Returns last collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->lastElement();
     * => 2
     * ```
     *
     * @return TV
     */
    public function lastElement(): mixed;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> NonEmptyLinkedList::collect(['1', '2'])->fold('0')(fn($acc, $cur) => $acc . $cur);
     * => '012'
     * ```
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldingOperation<TV, TVO>
     *
     * @see FoldMethodReturnTypeProvider
     */
    public function fold(mixed $init): FoldingOperation;

    /**
     * Displays all elements of this collection in a string
     * using start, end, and separator strings.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->mkString("(", ",", ")")
     * => '(1,2,3)'
     * ```
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string;
}
