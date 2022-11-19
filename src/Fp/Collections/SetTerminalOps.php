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
interface SetTerminalOps
{
    /**
     * Check if the element is present in the set
     * Alias for {@see SetOps::contains}
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])(1);
     * => true
     *
     * >>> HashSet::collect([1, 1, 2])(3);
     * => false
     * ```
     *
     * @param TV $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->contains(1);
     * => true
     *
     * >>> HashSet::collect([1, 1, 2])->contains(3);
     * => false
     * ```
     *
     * @param TV $element
     */
    public function contains(mixed $element): bool;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> HashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Same as {@see SetTerminalOps::every()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool;

    /**
     * Suppose you have an HashSet<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<HashSet<TVO>>
     * i.e. an Some<HashSet<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(HashSet(1, 2, 3))
     *
     * >>> HashSet::collect([0, 1, 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<Set<TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<Set<TVO>>
     */
    public function traverseOptionN(callable $callback): Option;

    /**
     * Same as {@see SetTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> HashSet::collect([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(HashSet(1, 2, 3))
     *
     * >>> HashSet::collect([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is Set<Option<TVO>>
     *
     * @return Option<Set<TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Suppose you have an Set<TV> and you want to format each element with a function that returns an Either<E, TVO>.
     * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, Set<TVO>>
     * i.e. an Right<Set<TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Right(HashSet(1, 2, 3))
     *
     * >>> HashSet::collect([0, 1, 2])->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, Set<TVO>>
     */
    public function traverseEither(callable $callback): Either;

    /**
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, Set<TVO>>
     */
    public function traverseEitherN(callable $callback): Either;

    /**
     * Same as {@see SetTerminalOps::traverseEither()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> HashSet::collect([Either::right(1), Either::right(2), Either::right(3)])->sequenceEither();
     * => Right(HashSet(1, 2, 3))
     *
     * >>> HashSet::collect([Either::left('err'), Either::right(1), Either::right(2)])->sequenceEither();
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is Set<Either<E, TVO>>
     *
     * @return Either<E, Set<TVO>>
     */
    public function sequenceEither(): Either;

    /**
     * Split collection to two parts by predicate function.
     * If $predicate returns true then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> HashSet::collect([0, 1, 2, 3, 4, 5])->partition(fn($i) => $i < 3);
     * => Separated(HashSet(3, 4, 5), HashSet(0, 1, 2))
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Separated<Set<TV>, Set<TV>>
     */
    public function partition(callable $predicate): Separated;

    /**
     * @param callable(mixed...): bool $predicate
     * @return Separated<Set<TV>, Set<TV>>
     */
    public function partitionN(callable $predicate): Separated;

    /**
     * Similar to {@see SetTerminalOps::partition()} but uses {@see Either} instead of bool.
     * So the output types LO/RO can be different from the input type TV.
     * If $callback returns Right then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> HashSet::collect([0, 1, 2, 3, 4, 5])
     * >>>     ->partitionMap(fn($i) => $i >= 5 ? Either::left("L: {$i}") : Either::right("R: {$i}"));
     * => Separated(HashSet('L: 5'), HashSet('R: 0', 'R: 1', 'R: 2', 'R: 3', 'R: 4'))
     * ```
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<Set<LO>, Set<RO>>
     */
    public function partitionMap(callable $callback): Separated;

    /**
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<Set<LO>, Set<RO>>
     */
    public function partitionMapN(callable $callback): Separated;

    /**
     * Produces a new Map of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * ```php
     * >>> $collection = HashSet::collect([1, 2, 2]);
     * => HashSet(1, 2)
     *
     * >>> $collection->reindex(fn($v) => "key-{$v}");
     * => HashMap('key-1' -> 1, 'key-2' -> 2)
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, TV>
     */
    public function reindex(callable $callback): Map;

    /**
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return Map<TKO, TV>
     */
    public function reindexN(callable $callback): Map;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> HashSet::collect([1, 2, 2])->exists(fn($elem) => 3 === $elem);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool;

    /**
     * Group elements
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2, 2, 3, 3])
     * >>>     ->groupBy(fn($i) => 0 === $i % 2 ? 'even' : 'odd')
     * => HashMap('odd' => NonEmptyHashSet(3, 1), 'even' => NonEmptyHashSet(2))
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return Map<TKO, NonEmptySet<TV>>
     */
    public function groupBy(callable $callback): Map;

    /**
     * ```php
     * >>> HashSet::collect([
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
     * =>   10 -> NonEmptyHashSet(21, 16, 11),
     * =>   20 -> NonEmptyHashSet(16, 11),
     * =>   30 -> NonEmptyHashSet(21),
     * => )
     * ```
     *
     * @template TKO
     * @template TVO
     *
     * @param callable(TV): TKO $group
     * @param callable(TV): TVO $map
     * @return Map<TKO, NonEmptySet<TVO>>
     */
    public function groupMap(callable $group, callable $map): Map;

    /**
     * Partitions this Set<TV> into a Map<TKO, TVO> according to a discriminator function $group.
     * All the values that have the same discriminator are then transformed by the $map and
     * then reduced into a single value with the $reduce.
     *
     *  * ```php
     * >>> HashSet::collect([
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
     * Fold many elements into one
     *
     * ```php
     * >>> HashSet::collect(['1', '2'])->fold('0')(fn($acc, $cur) => $acc . $cur);
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
     * Check if this set is subset of another set
     *
     * ```php
     * >>> HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2]));
     * => true
     *
     * >>> HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2, 3]));
     * => true
     *
     * >>> HashSet::collect([1, 2, 3])->subsetOf(HashSet::collect([1, 2]));
     * => false
     * ```
     */
    public function subsetOf(Set|NonEmptySet $superset): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * A combined {@see Set::first} and {@see Set::map}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> HashSet::collect(['zero', '1', '2'])
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
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function firstN(callable $predicate): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function lastN(callable $predicate): Option;

    /**
     * A combined {@see Set::last} and {@see Set::map}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> HashSet::collect(['zero', '1', '2'])
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
     * Return first collection element
     *
     * ```php
     * >>> HashSet::collect([1, 2])->head()->get();
     * => 1
     * ```
     *
     * @return Option<TV>
     */
    public function head(): Option;

    /**
     * Returns first collection element
     * Alias for {@see SetOps::head}
     *
     * ```php
     * >>> HashSet::collect([1, 2])->firstElement()->get();
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
     * >>> HashSet::collect([1, 2])->lastElement()->get();
     * => 2
     * ```
     *
     * @return Option<TV>
     */
    public function lastElement(): Option;

    /**
     * Displays all elements of this collection in a string
     * using start, end, and separator strings.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->mkString("(", ",", ")")
     * => '(1,2,3)'
     *
     * >>> HashSet::collect([])->mkString("(", ",", ")")
     * => '()'
     * ```
     */
    public function mkString(string $start = '', string $sep = ',', string $end = ''): string;

    /**
     * Returns the maximum value from collection
     *
     * ```php
     * >>> HashSet::collect([1, 4, 2])->max()->get();
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
     * >>> HashSet::collect([new Foo(1), new Bar(6), new Foo(2)])->maxBy(fn(Foo $foo) => $foo->a)->get();
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
     * >>> HashSet::collect([1, 4, 2])->min()->get();
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
     * >>> HashSet::collect([new Foo(1), new Bar(6), new Foo(2)])->minBy(fn(Foo $foo) => $foo->a)->get();
     * => Foo(1)
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return Option<TV>
     */
    public function minBy(callable $callback): Option;

    public function isEmpty(): bool;
}
