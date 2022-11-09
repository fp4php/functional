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
interface NonEmptySetTerminalOps
{
    /**
     * Check if the element is present in the set
     * Alias for {@see SetOps::contains}
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])(1);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])(3);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->contains(1);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->contains(3);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->every(fn($elem) => $elem > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Same as {@see NonEmptySetChainableOps::every()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmptyNonEmpty([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmptyNonEmpty([new Foo(1), new Bar(2)])->everyOf(Foo::class);
     * => false
     * ```
     *
     * @template TVO
     * @psalm-assert-if-true NonEmptySet<TVO> $this
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function everyOf(string|array $fqcn, bool $invariant = false): bool;

    /**
     * Suppose you have an NonEmptyHashSet<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<NonEmptyHashSet<TVO>>
     * i.e. an Some<NonEmptyHashSet<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> NonEmptyHashSet::collect([1, 2, 3])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyHashSet(1, 2, 3))
     *
     * >>> NonEmptyHashSet::collect([0, 1, 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptySet<TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * Same as {@see NonEmptySetChainableOps::traverseOption()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<NonEmptySet<TVO>>
     */
    public function traverseOptionN(callable $callback): Option;

    /**
     * Same as {@see SeqTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(NonEmptyHashSet(1, 2, 3))
     *
     * >>> NonEmptyHashSet::collectNonEmpty([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is NonEmptySet<Option<TVO>>
     *
     * @return Option<NonEmptySet<TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Suppose you have an NonEmptySet<TV> and you want to format each element with a function that returns an Either<E, TVO>.
     * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, NonEmptySet<TVO>>
     * i.e. an Right<NonEmptySet<TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])
     * >>>     ->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Right(NonEmptyHashSet(1, 2, 3))
     *
     * >>> NonEmptyHashSet::collectNonEmpty([0, 1, 2])
     * >>>     ->traverseEither(fn($x) => $x >= 1 ? Either::right($x) : Either::left('err'));
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, NonEmptySet<TVO>>
     */
    public function traverseEither(callable $callback): Either;

    /**
     * Same as {@see NonEmptySetChainableOps::traverseEither()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, NonEmptySet<TVO>>
     */
    public function traverseEitherN(callable $callback): Either;

    /**
     * Same as {@see NonEmptySetTerminalOps::traverseEither()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([
     * >>>     Either::right(1),
     * >>>     Either::right(2),
     * >>>     Either::right(3),
     * >>> ])->sequenceEither();
     * => Right(NonEmptyHashSet(1, 2, 3))
     *
     * >>> NonEmptyHashSet::collectNonEmpty([
     * >>>     Either::left('err'),
     * >>>     Either::right(1),
     * >>>     Either::right(2),
     * >>> ])->sequenceEither();
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is NonEmptySet<Either<E, TVO>>
     *
     * @return Either<E, NonEmptySet<TVO>>
     */
    public function sequenceEither(): Either;

    /**
     * Split collection to two parts by predicate function.
     * If $predicate returns true then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([0, 1, 2, 3, 4, 5])->partition(fn($i) => $i < 3);
     * => Separated(HashSet(3, 4, 5), HashSet(0, 1, 2))
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Separated<Set<TV>, Set<TV>>
     */
    public function partition(callable $predicate): Separated;

    /**
     * Same as {@see NonEmptySetChainableOps::partition()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<Set<TV>, Set<TV>>
     */
    public function partitionN(callable $predicate): Separated;

    /**
     * Similar to {@see NonEmptySetTerminalOps::partition()} but uses {@see Either} instead of bool.
     * So the output types LO/RO can be different from the input type TV.
     * If $callback returns Right then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([0, 1, 2, 3, 4, 5])
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
     * Same as {@see NonEmptySetChainableOps::partitionMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<Set<LO>, Set<RO>>
     */
    public function partitionMapN(callable $callback): Separated;

    /**
     * Produces a new NonEmptyMap of elements by assigning the values to keys generated by a transformation function (callback).
     *
     * ```php
     * >>> $collection = NonEmptyHashSet::collectNonEmpty([1, 2, 2]);
     * => HashSet(1, 2)
     *
     * >>> $collection->reindex(fn($v) => "key-{$v}");
     * => NonEmptyHashMap('key-1' -> 1, 'key-2' -> 2)
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, TV>
     */
    public function reindex(callable $callback): NonEmptyMap;

    /**
     * Same as {@see NonEmptySetChainableOps::reindex()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TKO
     *
     * @param callable(mixed...): TKO $callback
     * @return NonEmptyMap<TKO, TV>
     */
    public function reindexN(callable $callback): NonEmptyMap;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->exists(fn($elem) => 3 === $elem);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Same as {@see NonEmptySetChainableOps::exists()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, new Foo(2)])->existsOf(Bar::class);
     * => false
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function existsOf(string|array $fqcn, bool $invariant = false): bool;

    /**
     * Group elements
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2, 2, 3, 3])
     * >>>     ->groupBy(fn($i) => 0 === $i % 2 ? 'even' : 'odd')
     * => NonEmptyHashMap('odd' => NonEmptyHashSet(3, 1), 'even' => NonEmptyHashSet(2))
     * ```
     *
     * @template TKO
     *
     * @param callable(TV): TKO $callback
     * @return NonEmptyMap<TKO, NonEmptySet<TV>>
     */
    public function groupBy(callable $callback): NonEmptyMap;

    /**
     * Combinator of {@see NonEmptySetTerminalOps::groupBy()} and {@see NonEmptySetChainableOps::map()}.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([
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
     * => NonEmptyHashMap(
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
     * @return NonEmptyMap<TKO, NonEmptySet<TVO>>
     */
    public function groupMap(callable $group, callable $map): NonEmptyMap;

    /**
     * Partitions this NonEmptySet<TV> into a NonEmptyMap<TKO, TVO> according to a discriminator function $group.
     * All the values that have the same discriminator are then transformed by the $map and
     * then reduced into a single value with the $reduce.
     *
     *  * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([
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
     * Fold many elements into one
     *
     * ```php
     * >>> NonEmptyHashSet::collect(['1', '2'])->fold('0')(fn($acc, $cur) => $acc . $cur);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2]));
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2, 3]));
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2]));
     * => false
     * ```
     */
    public function subsetOf(Set|NonEmptySet $superset): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Same as {@see NonEmptySetChainableOps::first()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function firstN(callable $predicate): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Same as {@see NonEmptySetChainableOps::last()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Option<TV>
     */
    public function lastN(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([new Bar(1), new Foo(2), new Foo(3)])
     * >>>     ->firstOf(Foo::class)
     * >>>     ->get();
     * => Foo(2)
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return Option<TVO>
     */
    public function firstOf(string|array $fqcn, bool $invariant = false): Option;

    /**
     * Return first collection element
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->head();
     * => 1
     * ```
     *
     * @return TV
     */
    public function head(): mixed;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySetOps::head}
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->firstElement();
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->lastElement();
     * => 2
     * ```
     *
     * @return TV
     */
    public function lastElement(): mixed;

    /**
     * Produces new set with given element excluded
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->removed(2)->toList();
     * => [1]
     * ```
     *
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set;

    /**
     * Filter collection by condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->filter(fn($elem) => $elem > 1)->toList();
     * => [2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Set<TV>
     *
     * @see CollectionFilterMethodReturnTypeProvider
     */
    public function filter(callable $predicate): Set;

    /**
     * Same as {@see NonEmptySetChainableOps::filter()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Set<TV>
     */
    public function filterN(callable $predicate): Set;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, new Foo(2)])->filterOf(Foo::class)->toList();
     * => [Foo(2)]
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return Set<TVO>
     */
    public function filterOf(string|array $fqcn, bool $invariant = false): Set;

    /**
     * A combined {@see NonEmptySet::map} and {@see NonEmptySet::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toList();
     * => [1, 2]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Set<TVO>
     */
    public function filterMap(callable $callback): Set;

    /**
     * Same as {@see NonEmptySetChainableOps::filterMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Set<TVO>
     */
    public function filterMapN(callable $callback): Set;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, null])->filterNotNull()->toList();
     * => [1]
     * ```
     *
     * @return Set<TV>
     */
    public function filterNotNull(): Set;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->tail()->toList();
     * => [2, 3]
     * ```
     *
     * @return Set<TV>
     */
    public function tail(): Set;

    /**
     * Returns every collection element except last
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->init()->toList();
     * => [1, 2]
     * ```
     *
     * @return Set<TV>
     */
    public function init(): Set;

    /**
     * Computes the intersection between this set and another set.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])
     *     ->intersect(HashSet::collect([2, 3]))->toList();
     * => [2, 3]
     * ```
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return Set<TV>
     */
    public function intersect(Set|NonEmptySet $that): Set;

    /**
     * Computes the difference of this set and another set.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])
     *     ->diff(HashSet::collect([2, 3]))->toList();
     * => [1]
     * ```
     *
     * @param Set<TV>|NonEmptySet<TV> $that
     * @return Set<TV>
     */
    public function diff(Set|NonEmptySet $that): Set;

    /**
     * Returns the maximum value from collection
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 4, 2])->max();
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
     * >>> NonEmptyHashSet::collectNonEmpty([new Foo(1), new Bar(6), new Foo(2)])->maxBy(fn(Foo $foo) => $foo->a);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 4, 2])->min();
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
     * >>> NonEmptyHashSet::collectNonEmpty([new Foo(1), new Bar(6), new Foo(2)])->minBy(fn(Foo $foo) => $foo->a);
     * => Foo(1)
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return TV
     */
    public function minBy(callable $callback): mixed;
}
