<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

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
     * Produces new set with given element included
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->updated(3)->toArray()
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
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, 2])->removed(2)->toArray()
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
     * Filter collection by condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->filter(fn($elem) => $elem > 1)->toArray()
     * => [2]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Set<TV>
     */
    public function filter(callable $predicate): Set;

    /**
     * A combined {@see NonEmptySet::map} and {@see NonEmptySet::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     * Also, NonEmpty* prefix will be lost.
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toArray()
     * => [1, 2]
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return Set<TVO>
     */
    public function filterMap(callable $callback): Set;

    /**
     * Exclude null elements
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 1, null])->filterNotNull()->toArray()
     * => [1]
     *
     * @psalm-return Set<TV>
     */
    public function filterNotNull(): Set;

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
     * Find first element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->first(fn($elem) => $elem > 1)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 0, 2])->last(fn($elem) => $elem > 0)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get()
     * => Foo(2)
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
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->head()
     * => 1
     *
     * @psalm-return TV
     */
    public function head(): mixed;

    /**
     * Returns every collection element except first
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 3])->tail()->toArray()
     * => [2, 3]
     *
     * @psalm-return Set<TV>
     */
    public function tail(): Set;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySetOps::head}
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->firstElement()
     * => 1
     *
     * @psalm-return TV
     */
    public function firstElement(): mixed;

    /**
     * Returns last collection element
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2])->lastElement()
     * => 2
     *
     * @psalm-return TV
     */
    public function lastElement(): mixed;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> NonEmptyHashSet::collectNonEmpty([1, 2, 2])->map(fn($elem) => (string) $elem)->toArray()
     * => ['1', '2']
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptySet<TVO>
     */
    public function map(callable $callback): NonEmptySet;

    /**
     * Call a function for every collection element
     *
     * REPL:
     * >>> NonEmptyHashSet::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray()
     * => [2, 3]
     *
     * @param callable(TV): void $callback
     * @psalm-return NonEmptySet<TV>
     */
    public function tap(callable $callback): NonEmptySet;
}
