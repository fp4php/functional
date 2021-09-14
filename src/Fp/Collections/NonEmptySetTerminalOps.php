<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySetTerminalOps
{
    /**
     * Check if the element is present in the set
     * Alias for @see SetOps::contains
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])(1)
     * => true
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])(3)
     * => false
     *
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->contains(1)
     * => true
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->contains(3)
     * => false
     *
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->every(fn($elem) => $elem > 0)
     * => true
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->every(fn($elem) => $elem > 1)
     * => false
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmptyNonEmpty([new Foo(1), new Foo(2)])->everyOf(Foo::class)
     * => true
     * >>> NonEmptyHashSet::collectNonEmptyNonEmpty([new Foo(1), new Bar(2)])->everyOf(Foo::class)
     * => false
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->exists(fn($elem) => 2 === $elem)
     * => true
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->exists(fn($elem) => 3 === $elem)
     * => false
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, new Foo(2)])->existsOf(Foo::class)
     * => true
     * >>> NonEmptyHashSet::collectNonEmpty([1, new Foo(2)])->existsOf(Bar::class)
     * => false
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Reduce multiple elements into one
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty(['1', '2', '2'])->reduce(fn($acc, $cur) => $acc . $cur)
     * => '12'
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed;

    /**
     * Check if this set is subset of another set
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([1, 2])->subsetOf(NonEmptyHashSet::collect([1, 2]))
     * => true
     * >>> NonEmptyHashSet::collect([1, 2])->subsetOf(NonEmptyHashSet::collect([1, 2, 3]))
     * => true
     * >>> NonEmptyHashSet::collect([1, 2, 3])->subsetOf(NonEmptyHashSet::collect([1, 2]))
     * => false
     */
    public function subsetOf(Set|NonEmptySet $superset): bool;
}
