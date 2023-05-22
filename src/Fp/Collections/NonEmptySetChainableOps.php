<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Psalm\Hook\MethodReturnTypeProvider\MapTapNMethodReturnTypeProvider;

/**
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface NonEmptySetChainableOps
{
    /**
     * Produces new set with given element included
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->updated(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $element
     * @return NonEmptySet<TV|TVI>
     */
    public function updated(mixed $element): NonEmptySet;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return NonEmptySet<TVO>
     */
    public function map(callable $callback): NonEmptySet;

    /**
     * Same as {@see NonEmptySetChainableOps::map()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): TVO $callback
     * @return NonEmptySet<TVO>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function mapN(callable $callback): NonEmptySet;

    /**
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([2, 5])
     * >>>     ->flatMap(fn($e) => [$e - 1, $e, $e, $e + 1])
     * >>>     ->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptySet<TVO>
     */
    public function flatMap(callable $callback): NonEmptySet;

    /**
     * Same as {@see NonEmptySetChainableOps::flatMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): (non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>) $callback
     * @return NonEmptySet<TVO>
     */
    public function flatMapN(callable $callback): NonEmptySet;

    /**
     * Converts this NonEmptySet<iterable<TVO>> into a Set<TVO>.
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([
     * >>>     HashSet::collect([1, 2]),
     * >>>     HashSet::collect([3, 4]),
     * >>>     HashSet::collect([5, 6]),
     * >>> ])->flatten();
     * => HashSet(1, 2, 3, 4, 5, 6)
     * ```
     *
     * @template TVO
     * @psalm-if-this-is NonEmptySet<non-empty-array<array-key, TVO>|NonEmptyCollection<mixed, TVO>>
     *
     * @return NonEmptySet<TVO>
     */
    public function flatten(): NonEmptySet;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return NonEmptySet<TV>
     */
    public function tap(callable $callback): NonEmptySet;

    /**
     * Same as {@see NonEmptySetChainableOps::tap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @param callable(mixed...): void $callback
     * @return NonEmptySet<TV>
     */
    public function tapN(callable $callback): NonEmptySet;
}
