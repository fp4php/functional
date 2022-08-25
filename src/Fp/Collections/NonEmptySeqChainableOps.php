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
     * @param (iterable<TVI>|Collection<TVI>|NonEmptyCollection<TVI>) $suffix
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
     * @param (iterable<TVI>|Collection<TVI>|NonEmptyCollection<TVI>) $prefix
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
     * Sort collection
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toList();
     * => [1, 2, 3]
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toList();
     * => [3, 2, 1]
     * ```
     *
     * @param callable(TV, TV): int $cmp
     * @return NonEmptySeq<TV>
     */
    public function sorted(callable $cmp): NonEmptySeq;

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
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> NonEmptyArrayList::collectNonEmpty([1, 2, 3])->zip([4, 5, 6, 7]);
     * => NonEmptyArrayList([1, 4], [2, 5], [3, 6])
     * ```
     *
     * @template TVI
     *
     * @param non-empty-array<TVI> | NonEmptyCollection<TVI> $that
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
}
