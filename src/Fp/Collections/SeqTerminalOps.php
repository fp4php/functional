<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Operations\FoldOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\FoldMethodReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\MapTapNMethodReturnTypeProvider;
use function Fp\id;

/**
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
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
     * Same as {@see SeqTerminalOps::every()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): bool $predicate
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function everyN(callable $predicate): bool;

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
     * Same as {@see SeqTerminalOps::traverseOption()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<Seq<TVO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function traverseOptionN(callable $callback): Option;

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
     * Suppose you have an Seq<TV> and you want to format each element with a function that returns an Either<E, TVO>.
     * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, Seq<TVO>>
     * i.e. an Right<Seq<TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Right(ArrayList(1, 2, 3))
     *
     * >>> ArrayList::collect([0, 1, 2])->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, Seq<TVO>>
     */
    public function traverseEither(callable $callback): Either;

    /**
     * Same as {@see SeqTerminalOps::traverseEither()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, Seq<TVO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function traverseEitherN(callable $callback): Either;

    /**
     * Same as {@see SeqTerminalOps::traverseEither()}, but collects all errors to non-empty-list.
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, Seq<TVO>>
     */
    public function traverseEitherMerged(callable $callback): Either;

    /**
     * Same as {@see SeqTerminalOps::traverseEitherMerged()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, Seq<TVO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function traverseEitherMergedN(callable $callback): Either;

    /**
     * Same as {@see SeqTerminalOps::traverseEither()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> ArrayList::collect([Either::right(1), Either::right(2), Either::right(3)])->sequenceEither();
     * => Right(ArrayList(1, 2, 3))
     *
     * >>> ArrayList::collect([Either::left('err'), Either::right(1), Either::right(2)])->sequenceEither();
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is Seq<Either<E, TVO>>
     *
     * @return Either<E, Seq<TVO>>
     */
    public function sequenceEither(): Either;

    /**
     * Same as {@see Seq::sequenceEither()} but merge all left errors into non-empty-list.
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is Seq<Either<non-empty-list<E>, TVO>>
     *
     * @return Either<non-empty-list<E>, Seq<TVO>>
     */
    public function sequenceEitherMerged(): Either;

    /**
     * Split collection to two parts by predicate function.
     * If $predicate returns true then item gonna to right.
     * Otherwise, to left.
     *
     * ```php
     * >>> ArrayList::collect([0, 1, 2, 3, 4, 5])->partition(fn($i) => $i < 3);
     * => Separated(ArrayList(3, 4, 5), ArrayList(0, 1, 2))
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Separated<Seq<TV>, Seq<TV>>
     */
    public function partition(callable $predicate): Separated;

    /**
     * Same as {@see SeqTerminalOps::partition()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<Seq<TV>, Seq<TV>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function partitionN(callable $predicate): Separated;

    /**
     * Similar to {@see SeqTerminalOps::partition()} but uses {@see Either} instead of bool.
     * So the output types LO/RO can be different from the input type TV.
     * If $callback returns Right then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> ArrayList::collect([0, 1, 2, 3, 4, 5])
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
     * Same as {@see SeqTerminalOps::partitionMap()}, but deconstruct input tuple and pass it to the $callback function.
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
     * Same as {@see SeqTerminalOps::reindex()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return HashMap<TKO, TV>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function reindexN(callable $callback): HashMap;

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
     * Same as {@see SeqTerminalOps::exists()}, but deconstruct input tuple and pass it to the $callback function.
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
     * >>> LinkedList::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Same as {@see SeqTerminalOps::first()}, but deconstruct input tuple and pass it to the $callback function.
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
     * >>> LinkedList::collect(['zero', '1', '2'])
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
     * @return FoldOperation<TV, TVO>
     *
     * @see FoldMethodReturnTypeProvider
     */
    public function fold(mixed $init): FoldOperation;

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
     * Same as {@see SeqTerminalOps::last()}, but deconstruct input tuple and pass it to the $callback function.
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
     * >>> LinkedList::collect(['zero', '1', '2'])
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

    /**
     * Returns the maximum value from collection
     *
     * ```php
     * >>> LinkedList::collect([1, 4, 2])->max()->get();
     * => 4
     * ```
     *
     * @return Option<TV>
     */
    public function max(): Option;

    /**
     * Returns the maximum value from collection by iterating each element using the callback
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Bar(6), new Foo(2)])->maxBy(fn(Foo $foo) => $foo->a)->get();
     * => Bar(6)
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function maxBy(callable $callback): Option;

    /**
     * Returns the minimum value from collection
     *
     * ```php
     * >>> LinkedList::collect([1, 4, 2])->min()->get();
     * => 1
     * ```
     *
     * @return Option<TV>
     */
    public function min(): Option;

    /**
     * Returns the minimum value from collection by iterating each element using the callback
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Bar(6), new Foo(2)])->minBy(fn(Foo $foo) => $foo->a)->get();
     * => Foo(1)
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function minBy(callable $callback): Option;
}
