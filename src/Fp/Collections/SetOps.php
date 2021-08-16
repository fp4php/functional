<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV of (object|scalar)
 */
interface SetOps
{
    /**
     * Check if the element is present in the set
     * Alias for @see SetOps::contains
     *
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool;

    /**
     * Produces new set with given element included
     *
     * @template TVI of (object|scalar)
     * @param TVI $element
     * @return Set<TV|TVI>
     */
    public function updated(mixed $element): Set;

    /**
     * Produces new set with given element excluded
     *
     * @param TV $element
     * @return Set<TV>
     */
    public function removed(mixed $element): Set;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Filter collection by condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set;

    /**
     * @psalm-template TVO of (object|scalar)
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function flatMap(callable $callback): Set;

    /**
     * Fold many elements into one
     *
     * @psalm-param TV $init initial accumulator value
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current element): new accumulator
     * @psalm-return TV
     */
    public function fold(mixed $init, callable $callback): mixed;

    /**
     * Reduce multiple elements into one
     * Returns None for empty collection
     *
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV>
     */
    public function reduce(callable $callback): Option;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * @template TVO of (object|scalar)
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return Set<TVO>
     */
    public function map(callable $callback): Set;
}
