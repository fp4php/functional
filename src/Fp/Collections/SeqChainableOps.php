<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Psalm\Hook\MethodReturnTypeProvider\CollectionFilterMethodReturnTypeProvider;
use Fp\Psalm\Hook\MethodReturnTypeProvider\MapTapNMethodReturnTypeProvider;

/**
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface SeqChainableOps
{
    /**
     * Add element to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appended(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return Seq<TV|TVI>
     */
    public function appended(mixed $elem): Seq;

    /**
     * Add elements to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appendedAll([3, 4])->toList();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $suffix
     * @return Seq<TV|TVI>
     */
    public function appendedAll(iterable $suffix): Seq;

    /**
     * Add element to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prepended(0)->toList();
     * => [0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return Seq<TV|TVI>
     */
    public function prepended(mixed $elem): Seq;

    /**
     * Add elements to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prependedAll(-1, 0)->toList();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $prefix
     * @return Seq<TV|TVI>
     */
    public function prependedAll(iterable $prefix): Seq;

    /**
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->filter(fn($elem) => $elem > 1)->toList();
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
     * Same as {@see SeqChainableOps::filter()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Seq<TV>
     */
    public function filterN(callable $predicate): Seq;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, null])->filterNotNull()->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function filterNotNull(): Seq;

    /**
     * A combined {@see Seq::map} and {@see Seq::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> LinkedList::collect(['zero', '1', '2'])
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
     * Same as {@see SeqChainableOps::filterMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Seq<TVO>
     */
    public function filterMapN(callable $callback): Seq;

    /**
     * Converts this Seq<iterable<TVO>> into a Seq<TVO>.
     *
     * ```php
     * >>> LinkedList::collect([
     * >>>     LinkedList::collect([1, 2]),
     * >>>     LinkedList::collect([3, 4]),
     * >>>     LinkedList::collect([5, 6]),
     * >>> ])->flatten();
     * => LinkedList(1, 2, 3, 4, 5, 6)
     * ```
     *
     * @template TVO
     * @psalm-if-this-is Seq<iterable<mixed, TVO>|Collection<mixed, TVO>>
     *
     * @return Seq<TVO>
     */
    public function flatten(): Seq;

    /**
     * Map collection and then flatten the result
     *
     * ```php
     * >>> LinkedList::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): (iterable<mixed, TVO>|Collection<mixed, TVO>) $callback
     * @return Seq<TVO>
     */
    public function flatMap(callable $callback): Seq;

    /**
     * Same as {@see SeqChainableOps::flatMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): (iterable<mixed, TVO>|Collection<mixed, TVO>) $callback
     * @return Seq<TVO>
     */
    public function flatMapN(callable $callback): Seq;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return Seq<TVO>
     */
    public function map(callable $callback): Seq;

    /**
     * Same as {@see SeqChainableOps::map()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return Seq<TVO>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function mapN(callable $callback): Seq;

    /**
     * Copy collection in reversed order
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->reverse()->toList();
     * => [2, 1]
     * ```
     *
     * @return Seq<TV>
     */
    public function reverse(): Seq;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->tail()->toList();
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
     * >>> LinkedList::collect([1, 2, 3])->init()->toList();
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
     * >>> LinkedList::collect([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toList();
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
     * >>> LinkedList::collect([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toList();
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
     * >>> LinkedList::collect([1, 2, 3])->take(2)->toList();
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
     * >>> LinkedList::collect([1, 2, 3])->drop(2)->toList();
     * => [3]
     * ```
     *
     * @return Seq<TV>
     */
    public function drop(int $length): Seq;

    /**
     * Ascending sort
     *
     * ```php
     * >>> LinkedList::collect([2, 1, 3])->sorted();
     * => LinkedList(1, 2, 3)
     * ```
     *
     * @return Seq<TV>
     */
    public function sorted(): Seq;

    /**
     * Ascending sort by specific value
     *
     * ```php
     * >>> LinkedList::collect([new Foo(2), new Foo(1), new Foo(3)])
     *         ->sortedBy(fn(Foo $obj) => $obj->a);
     * => LinkedList(Foo(1), Foo(2), Foo(3))
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return Seq<TV>
     */
    public function sortedBy(callable $callback): Seq;

    /**
     * Descending sort
     *
     * ```php
     * >>> LinkedList::collect([2, 1, 3])->sorted();
     * => LinkedList(3, 2, 1)
     * ```
     *
     * @return Seq<TV>
     */
    public function sortedDesc(): Seq;

    /**
     * Descending sort by specific value
     *
     * ```php
     * >>> LinkedList::collect([new Foo(2), new Foo(1), new Foo(3)])
     *         ->sortedBy(fn(Foo $obj) => $obj->a);
     * => LinkedList(Foo(3), Foo(2), Foo(1))
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return Seq<TV>
     */
    public function sortedDescBy(callable $callback): Seq;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return Seq<TV>
     */
    public function tap(callable $callback): Seq;

    /**
     * Same as {@see SeqChainableOps::tap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): void $callback
     * @return Seq<TV>
     */
    public function tapN(callable $callback): Seq;

    /**
     * Add specified separator between every pair of elements in the source collection.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->intersperse(0)->toList();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return Seq<TV | TVI>
     */
    public function intersperse(mixed $separator): Seq;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->zip([4, 5, 6, 7])->toList();
     * => [[1, 4], [2, 5], [3, 6]]
     * ```
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $that
     * @return Seq<array{TV, TVI}>
     */
    public function zip(iterable $that): Seq;

    /**
     * Zips each collection element with their indexes
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->zipWithKeys();
     * => ArrayList([0, 1], [1, 2], [2, 3])
     * ```
     *
     * @return Seq<array{int, TV}>
     */
    public function zipWithKeys(): Seq;

    /**
     * ```php
     * >>> ArrayList::collect([['n' => 1], ['n' => 1], ['n' => 2]])->uniqueBy(fn($x) => $x['n'])
     * => ArrayList(['n' => 1], ['n' => 2])
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return Seq<TV>
     */
    public function uniqueBy(callable $callback): Seq;
}
