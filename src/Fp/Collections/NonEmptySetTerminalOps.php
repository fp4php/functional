<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface NonEmptySetTerminalOps
{
    /**
     * Check if the element is present in the set
     * Alias for {@see SetOps::contains}
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])(1);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])(3);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->contains(1);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->contains(3);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->every(fn($elem) => $elem > 1);
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
     * >>> NonEmptyHashSet::collectNonEmptyNonEmpty([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmptyNonEmpty([new Foo(1), new Bar(2)])->everyOf(Foo::class);
     * => false
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * A combined {@see Set::map} and {@see Set::every}.
     *
     * Predicate satisfying is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> NonEmptyHashSet::collect([1, 2, 3])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyHashSet(1, 2, 3))
     *
     * >>> NonEmptyHashSet::collect([0, 1, 2])->everyMap(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return Option<NonEmptySet<TVO>>
     */
    public function everyMap(callable $callback): Option;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->exists(fn($elem) => 3 === $elem);
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, new Foo(2)])->existsOf(Bar::class);
     * => false
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Reduce multiple elements into one
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty(['1', '2', '2'])->reduce(fn($acc, $cur) => $acc . $cur);
     * => '12'
     * ```
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed;

    /**
     * Check if this set is subset of another set
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2]));
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2, 3]));
     * => true
     *
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->subsetOf(NonEmptyHashSet::collectNonEmpty([1, 2]));
     * => false
     * ```
     */
    public function subsetOf(Set|NonEmptySet $superset): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
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
     * >>> NonEmptyHashSet::collectNonEmpty([new Bar(1), new Foo(2), new Foo(3)])
     * >>>     ->firstOf(Foo::class)
     * >>>     ->get();
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->head();
     * => 1
     * ```
     *
     * @psalm-return TV
     */
    public function head(): mixed;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySetOps::head}
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->firstElement();
     * => 1
     * ```
     *
     * @psalm-return TV
     */
    public function firstElement(): mixed;

    /**
     * Returns last collection element
     *
     * ```php
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->lastElement();
     * => 2
     * ```
     *
     * @psalm-return TV
     */
    public function lastElement(): mixed;
}
