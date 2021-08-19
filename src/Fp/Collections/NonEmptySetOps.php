<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySetOps
{
    /**
     * Check if the element is present in the set
     * Alias for @see SetOps::contains
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 1, 2])(1)
     * => true
     * >>> NonEmptyHashSet::collect([1, 1, 2])(3)
     * => false
     *
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 1, 2])->contains(1)
     * => true
     * >>> NonEmptyHashSet::collect([1, 1, 2])->contains(3)
     * => false
     *
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool;

    /**
     * Produces new set with given element included
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 1, 2])->updated(3)->toArray()
     * => [1, 2, 3]
     *
     * @template TVI
     * @param TVI $element
     * @return NonEmptySet<TV|TVI>
     */
    public function updated(mixed $element): NonEmptySet;

    /**
     * Produces new set with given element excluded
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 1, 2])->removed(2)->toArray()
     * => [1]
     *
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 0)
     * => true
     * >>> NonEmptyHashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 1)
     * => false
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 2, 2])->exists(fn($elem) => 2 === $elem)
     * => true
     * >>> NonEmptyHashSet::collect([1, 2, 2])->exists(fn($elem) => 3 === $elem)
     * => false
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Filter collection by condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 2, 2])->filter(fn($elem) => $elem > 1)->toArray()
     * => [2]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set;

    /**
     * Reduce multiple elements into one
     *
     * REPL:
     * >>> NonEmptyHashSet::collect(['1', '2', '2'])->reduce(fn($acc, $cur) => $acc . $cur)
     * => '12'
     *
     * @template TVI
     * @psalm-param callable(TV|TVI, TV): (TV|TVI) $callback (accumulator, current value): new accumulator
     * @psalm-return (TV|TVI)
     */
    public function reduce(callable $callback): mixed;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 2, 2])->map(fn($elem) => (string) $elem)->toArray()
     * => ['1', '2']
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptySet<TVO>
     */
    public function map(callable $callback): NonEmptySet;
}
