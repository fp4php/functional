<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
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
     */
    public function filter(callable $predicate): Set;

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
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Set<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Set;

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
     * @param callable(TV): (iterable<TVO>) $callback
     * @return Set<TVO>
     */
    public function flatMap(callable $callback): Set;

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
}
