<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Operations\FoldOperation;
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
     * Same as {@see NonEmptySeqTerminalOps::every()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function everyN(callable $predicate): bool;

    /**
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->filter(fn($elem) => $elem > 1)->toList();
     * => [2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Seq<TV>
     *
     * @see CollectionFilterMethodReturnTypeProvider
     */
    public function filter(callable $predicate): Seq;

    /**
     * Same as {@see NonEmptySeqTerminalOps::filter()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Seq<TV>
     */
    public function filterN(callable $predicate): Seq;

    /**
     * A combined {@see NonEmptySeq::map} and {@see NonEmptySeq::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toList();
     * => [1, 2]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Seq<TVO>
     */
    public function filterMap(callable $callback): Seq;

    /**
     * Same as {@see NonEmptySeqTerminalOps::filterMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Seq<TVO>
     */
    public function filterMapN(callable $callback): Seq;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, null])->filterNotNull()->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function filterNotNull(): Seq;

    /**
     * Converts this NonEmptySeq<iterable<TVO>> into a Seq<TVO>.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([
     * >>>     LinkedList::collect([1, 2]),
     * >>>     LinkedList::collect([3, 4]),
     * >>>     LinkedList::collect([5, 6]),
     * >>> ])->flatten();
     * => LinkedList(1, 2, 3, 4, 5, 6)
     * ```
     *
     * @template TVO
     * @psalm-if-this-is NonEmptySeq<non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>>
     *
     * @return NonEmptySeq<TVO>
     */
    public function flatten(): NonEmptySeq;

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
     * Same as {@see NonEmptySeqTerminalOps::traverseOption()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<NonEmptySeq<TVO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function traverseOptionN(callable $callback): Option;

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
     * Suppose you have an NonEmptySeq<TV> and you want to format each element with a function that returns an Either<E, TVO>.
     * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, NonEmptySeq<TVO>>
     * i.e. an Right<NonEmptySeq<TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 3])
     * >>>     ->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Right(NonEmptyArrayList(1, 2, 3))
     *
     * >>> NonEmptyArrayList::collectNonEmpty([0, 1, 2])
     * >>>     ->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, NonEmptySeq<TVO>>
     */
    public function traverseEither(callable $callback): Either;

    /**
     * Same as {@see NonEmptySeqTerminalOps::traverseEither()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, NonEmptySeq<TVO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function traverseEitherN(callable $callback): Either;

    /**
     * Same as {@see NonEmptySeqTerminalOps::traverseEither()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([
     * >>>     Either::right(1),
     * >>>     Either::right(2),
     * >>>     Either::right(3),
     * >>> ])->sequenceEither();
     * => Right(ArrayList(1, 2, 3))
     *
     * >>> NonEmptyArrayList::collectNonEmpty([
     * >>>     Either::left('err'),
     * >>>     Either::right(1),
     * >>>     Either::right(2),
     * >>> ])->sequenceEither();
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is NonEmptySeq<Either<E, TVO>>
     *
     * @return Either<E, NonEmptySeq<TVO>>
     */
    public function sequenceEither(): Either;

    /**
     * Split collection to two parts by predicate function.
     * If $predicate returns true then item gonna to right.
     * Otherwise, to left.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([0, 1, 2, 3, 4, 5])->partition(fn($i) => $i < 3);
     * => Separated(ArrayList(3, 4, 5), ArrayList(0, 1, 2))
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Separated<Seq<TV>, Seq<TV>>
     */
    public function partition(callable $predicate): Separated;

    /**
     * Same as {@see NonEmptySeqTerminalOps::partition()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<Seq<TV>, Seq<TV>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function partitionN(callable $predicate): Separated;

    /**
     * Similar to {@see NonEmptySeqTerminalOps::partition()} but uses {@see Either} instead of bool.
     * So the output types LO/RO can be different from the input type TV.
     * If $callback returns Right then item gonna to right.
     * Otherwise, to left.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([0, 1, 2, 3, 4, 5])
     * >>>     ->partitionMap(fn($i) => $i >= 5 ? Either::left("L: {$i}") : Either::right("R: {$i}"));
     * => Separated(ArrayList('L: 5'), ArrayList('R: 0', 'R: 1', 'R: 2', 'R: 3', 'R: 4'))
     * ```
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<Seq<LO>, Seq<RO>>
     */
    public function partitionMap(callable $callback): Separated;

    /**
     * Same as {@see NonEmptySeqTerminalOps::partitionMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<Seq<LO>, Seq<RO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function partitionMapN(callable $callback): Separated;

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
     * Same as {@see NonEmptySeqTerminalOps::reindex()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return NonEmptyMap<TKO, TV>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function reindexN(callable $callback): NonEmptyMap;

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
     * Same as {@see NonEmptySeqTerminalOps::exists()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function existsN(callable $predicate): bool;

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
     * Same as {@see NonEmptySeqTerminalOps::first()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function firstN(callable $predicate): Option;

    /**
     * A combined {@see Seq::first} and {@see Seq::map}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->firstMap(fn($elem) => Option::when(is_numeric($elem), fn() => (int) $elem));
     * => Some(1)
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<TVO>
     */
    public function firstMap(callable $callback): Option;

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
     * Returns every collection element except first
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->tail()->toList();
     * => [2, 3]
     * ```
     *
     * @return Seq<TV>
     */
    public function tail(): Seq;

    /**
     * Returns every collection element except last
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->init()->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function init(): Seq;

    /**
     * Take collection elements while predicate is true
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toList();
     * => [1, 2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Seq<TV>
     */
    public function takeWhile(callable $predicate): Seq;

    /**
     * Drop collection elements while predicate is true
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toList();
     * => [3]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Seq<TV>
     */
    public function dropWhile(callable $predicate): Seq;

    /**
     * Take N collection elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->take(2)->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function take(int $length): Seq;

    /**
     * Drop N collection elements
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->drop(2)->toList();
     * => [3]
     * ```
     *
     * @return Seq<TV>
     */
    public function drop(int $length): Seq;

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
     * Same as {@see NonEmptySeqTerminalOps::last()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function lastN(callable $predicate): Option;

    /**
     * A combined {@see Seq::last} and {@see Seq::map}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->lastMap(fn($elem) => Option::when(is_numeric($elem), fn() => (int) $elem));
     * => Some(2)
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<TVO>
     */
    public function lastMap(callable $callback): Option;

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
     * @return FoldOperation<TV, TVO>
     *
     * @see FoldMethodReturnTypeProvider
     */
    public function fold(mixed $init): FoldOperation;

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

    /**
     * Returns the maximum value from collection
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 4, 2])->max();
     * => 4
     * ```
     *
     * @return TV
     */
    public function max(): mixed;

    /**
     * Returns the maximum value from collection by iterating each element using the callback
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Bar(6), new Foo(2)])->maxBy(fn(Foo $foo) => $foo->a);
     * => Bar(6)
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function maxBy(callable $callback): mixed;

    /**
     * Returns the minimum value from collection
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 4, 2])->min();
     * => 1
     * ```
     *
     * @return TV
     */
    public function min(): mixed;

    /**
     * Returns the minimum value from collection by iterating each element using the callback
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Bar(6), new Foo(2)])->minBy(fn(Foo $foo) => $foo->a);
     * => Foo(1)
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function minBy(callable $callback): mixed;
}
