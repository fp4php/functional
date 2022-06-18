<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface SetChainableOps
{
    /**
     * Produces new set with given element included
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->updated(3)->toArray();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     * @param TVI $element
     * @return Set<TV|TVI>
     */
    public function updated(mixed $element): Set;

    /**
     * Produces new set with given element excluded
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->removed(2)->toArray();
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
     * >>> HashSet::collect([1, 2, 2])->filter(fn($elem) => $elem > 1)->toArray();
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
     * >>> HashSet::collect([1, 1, new Foo(2)])->filterOf(Foo::class)->toArray();
     * => [Foo(2)]
     * ```
     *
     * @template TVO
     * @param class-string<TVO> $fqcn fully qualified class name
     * @param bool $invariant if turned on then subclasses are not allowed
     * @return Set<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Set;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> HashSet::collect([1, 1, null])->filterNotNull()->toArray();
     * => [1]
     * ```
     *
     * @return Set<TV>
     */
    public function filterNotNull(): Set;

    /**
     * A combined {@see Set::map} and {@see Set::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> HashSet::collect(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toArray();
     * => [1, 2]
     * ```
     *
     * @template TVO
     * @param callable(TV): Option<TVO> $callback
     * @return Set<TVO>
     */
    public function filterMap(callable $callback): Set;

    /**
     * ```php
     * >>> HashSet::collect([2, 5, 5])->flatMap(fn($e) => [$e - 1, $e, $e, $e + 1])->toArray();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TVO
     * @param callable(TV): iterable<TVO> $callback
     * @return Set<TVO>
     */
    public function flatMap(callable $callback): Set;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->map(fn($elem) => (string) $elem)->toArray();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     * @param callable(TV): TVO $callback
     * @return Set<TVO>
     */
    public function map(callable $callback): Set;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->mapWithKey(fn($index, $elem) => "{$index}-{$key}")->toArray();
     * => ['0-1', '1-2']
     * ```
     *
     * @template TVO
     *
     * @param callable(int, TV): TVO $callback
     * @return Set<TVO>
     */
    public function mapWithKey(callable $callback): Set;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> HashSet::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return Set<TV>
     */
    public function tap(callable $callback): Set;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->tail()->toArray();
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
     * >>> HashSet::collect([1, 2, 3])
     *     ->intersect(HashSet::collect([2, 3]))->toArray();
     * => [2, 3]
     * ```
     *
     * @param Set<TV>|NonEmptySet<TV> $that the set to intersect with.
     * @return Set<TV> a new set consisting of all elements that are both in this
     * set and in the given set `that`.
     */
    public function intersect(Set|NonEmptySet $that): Set;

    /**
     * Computes the difference of this set and another set.
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])
     *     ->diff(HashSet::collect([2, 3]))->toArray();
     * => [1]
     * ```
     *
     * @param Set<TV>|NonEmptySet<TV> $that the set of elements to exclude.
     * @return Set<TV> a set containing those elements of this
     * set that are not also contained in the given set `that`.
     */
    public function diff(Set|NonEmptySet $that): Set;
}
