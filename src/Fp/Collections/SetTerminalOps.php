<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SetTerminalOps
{
    /**
     * Check if the element is present in the set
     * Alias for @see SetOps::contains
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])(1);
     * => true
     *
     * >>> HashSet::collect([1, 1, 2])(3);
     * => false
     * ```
     *
     * @psalm-param TV $element
     */
    public function __invoke(mixed $element): bool;

    /**
     * Check if the element is present in the set
     *
     * ```php
     * >>> HashSet::collect([1, 1, 2])->contains(1);
     * => true
     *
     * >>> HashSet::collect([1, 1, 2])->contains(3);
     * => false
     * ```
     *
     * @psalm-param TV $element
     */
    public function contains(mixed $element): bool;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> HashSet::collect([1, 2, 2])->every(fn($elem) => $elem > 1);
     * => false
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * ```php
     * >>> HashSet::collect([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> HashSet::collect([new Foo(1), new Bar(2)])->everyOf(Foo::class);
     * => false
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> HashSet::collect([1, 2, 2])->exists(fn($elem) => 3 === $elem);
     * => false
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * ```php
     * >>> HashSet::collect([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> HashSet::collect([1, new Foo(2)])->existsOf(Bar::class);
     * => false
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> HashSet::collect(['1', '2', '2'])->fold('0', fn($acc, $cur) => $acc . $cur);
     * => '012'
     * ```
     *
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed;

    /**
     * Reduce multiple elements into one
     * Returns None for empty collection
     *
     * ```php
     * >>> HashSet::collect(['1', '2', '2'])->reduce(fn($acc, $cur) => $acc . $cur)->get();
     * => '12'
     * ```
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option;

    /**
     * Check if this set is subset of another set
     *
     * ```php
     * >>> HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2]));
     * => true
     *
     * >>> HashSet::collect([1, 2])->subsetOf(HashSet::collect([1, 2, 3]));
     * => true
     *
     * >>> HashSet::collect([1, 2, 3])->subsetOf(HashSet::collect([1, 2]));
     * => false
     * ```
     */
    public function subsetOf(Set|NonEmptySet $superset): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> HashSet::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> HashSet::collect([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get();
     * => Foo(2)
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Return first collection element
     *
     * ```php
     * >>> HashSet::collect([1, 2])->head()->get();
     * => 1
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function head(): Option;

    /**
     * Returns first collection element
     * Alias for {@see SetOps::head}
     *
     * ```php
     * >>> HashSet::collect([1, 2])->firstElement()->get();
     * => 1
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option;

    /**
     * Returns last collection element
     *
     * ```php
     * >>> HashSet::collect([1, 2])->lastElement()->get();
     * => 2
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option;
}
