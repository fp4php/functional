<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Psalm\Hook\MethodReturnTypeProvider\MapTapNMethodReturnTypeProvider;

/**
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface NonEmptySeqChainableOps
{
    /**
     * Add element to the collection end
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->appended(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return NonEmptySeq<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptySeq;

    /**
     * Add elements to the collection end
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->appendedAll([3, 4])->toList();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $suffix
     * @return NonEmptySeq<TV|TVI>
     */
    public function appendedAll(iterable $suffix): NonEmptySeq;

    /**
     * Add element to the collection start
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->prepended(0)->toList();
     * => [0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return NonEmptySeq<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptySeq;

    /**
     * Add elements to the collection start
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->prependedAll(-1, 0)->toList();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param (iterable<mixed, TVI>|Collection<mixed, TVI>) $prefix
     * @return NonEmptySeq<TV|TVI>
     */
    public function prependedAll(iterable $prefix): NonEmptySeq;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptySeq<TVO>
     */
    public function map(callable $callback): NonEmptySeq;

    /**
     * Same as {@see NonEmptySeqChainableOps::map()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return NonEmptySeq<TVO>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function mapN(callable $callback): NonEmptySeq;

    /**
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptySeq<TVO>
     */
    public function flatMap(callable $callback): NonEmptySeq;

    /**
     * Same as {@see NonEmptySeqChainableOps::flatMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptySeq<TVO>
     */
    public function flatMapN(callable $callback): NonEmptySeq;

    /**
     * Copy collection in reversed order
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->reverse()->toList();
     * => [2, 1]
     * ```
     *
     * @return NonEmptySeq<TV>
     */
    public function reverse(): NonEmptySeq;

    /**
     * Ascending sort
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 1, 3])->sorted();
     * => NonEmptyLinkedList(1, 2, 3)
     * ```
     *
     * @return NonEmptySeq<TV>
     */
    public function sorted(): NonEmptySeq;

    /**
     * Ascending sort by specific value
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(2), new Foo(1), new Foo(3)])
     * >>>     ->sortedBy(fn(Foo $obj) => $obj->a);
     * => NonEmptyLinkedList(Foo(1), Foo(2), Foo(3))
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptySeq<TV>
     */
    public function sortedBy(callable $callback): NonEmptySeq;

    /**
     * Descending sort
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 1, 3])->sorted();
     * => NonEmptyLinkedList(3, 2, 1)
     * ```
     *
     * @return NonEmptySeq<TV>
     */
    public function sortedDesc(): NonEmptySeq;

    /**
     * Descending sort by specific value
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(2), new Foo(1), new Foo(3)])
     * >>>     ->sortedBy(fn(Foo $obj) => $obj->a);
     * => NonEmptyLinkedList(Foo(3), Foo(2), Foo(1))
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptySeq<TV>
     */
    public function sortedDescBy(callable $callback): NonEmptySeq;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return NonEmptySeq<TV>
     */
    public function tap(callable $callback): NonEmptySeq;

    /**
     * Same as {@see NonEmptySeqChainableOps::tap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): void $callback
     * @return NonEmptySeq<TV>
     */
    public function tapN(callable $callback): NonEmptySeq;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 3])->zip([4, 5, 6, 7]);
     * => NonEmptyArrayList([1, 4], [2, 5], [3, 6])
     * ```
     *
     * @template TVI
     *
     * @param non-empty-array<array-key, TVI> | NonEmptyCollection<mixed, TVI> $that
     * @return NonEmptySeq<array{TV, TVI}>
     */
    public function zip(iterable $that): NonEmptySeq;

    /**
     * Zips each collection element with their indexes
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 3])->zipWithKeys();
     * => NonEmptyArrayList([0, 1], [1, 2], [2, 3])
     * ```
     *
     * @return NonEmptySeq<array{int, TV}>
     */
    public function zipWithKeys(): NonEmptySeq;

    /**
     * Add specified separator between every pair of elements in the source collection.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 3])->intersperse(0)->toList();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return NonEmptySeq<TV | TVI>
     */
    public function intersperse(mixed $separator): NonEmptySeq;

    /**
     * ```php
     * >>> NonEmptyArrayList::collect([['n' => 1], ['n' => 1], ['n' => 2]])->uniqueBy(fn($x) => $x['n'])
     * => NonEmptyArrayList(['n' => 1], ['n' => 2])
     * ```
     *
     * @param callable(TV): mixed $callback
     * @return NonEmptySeq<TV>
     */
    public function uniqueBy(callable $callback): NonEmptySeq;
}
