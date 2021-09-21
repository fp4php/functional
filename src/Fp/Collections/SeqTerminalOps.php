<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface SeqTerminalOps
{
    /**
     * Find element by its index (Starts from zero).
     * Returns None if there is no such collection element.
     *
     * ```php
     * >>> ArrayList::collect([1, 2])(1)->get();
     * => 2
     * ```
     *
     * Alias for {@see Seq::at()}
     *
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option;

    /**
     * Find element by its index (Starts from zero)
     * Returns None if there is no such collection element
     *
     * ```php
     * >>> ArrayList::collect([1, 2])->at(1)->get();
     * => 2
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * and false otherwise
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> LinkedList::collect([1, 2])->every(fn($elem) => $elem > 1);
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
     * >>> LinkedList::collect([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> LinkedList::collect([new Foo(1), new Bar(2)])->everyOf(Foo::class);
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
     * >>> LinkedList::collect([1, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> LinkedList::collect([1, 2])->exists(fn($elem) => 3 === $elem);
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
     * >>> LinkedList::collect([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> LinkedList::collect([1, new Foo(2)])->existsOf(Bar::class);
     * => false
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find first element which satisfies the condition
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
     * => 2
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * ```php
     * >>> LinkedList::collect([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get();
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
     * Fold many elements into one
     *
     * ```php
     * >>> LinkedList::collect(['1', '2'])->fold('0', fn($acc, $cur) => $acc . $cur);
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
     * >>> LinkedList::collect(['1', '2'])->reduce(fn($acc, $cur) => $acc . $cur)->get();
     * => '12'
     * ```
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option;

    /**
     * Return first collection element
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->head()->get();
     * => 1
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function head(): Option;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> LinkedList::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first collection element
     * Alias for {@see SeqOps::head}
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->firstElement()->get();
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
     * >>> LinkedList::collect([1, 2])->lastElement()->get();
     * => 2
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option;

    /**
     * Check if collection has no elements
     *
     * ```php
     * >>> LinkedList::collect([])->isEmpty();
     * => true
     * ```
     */
    public function isEmpty(): bool;

    /**
     * Check if collection has no elements
     *
     * ```php
     * >>> LinkedList::collect([])->isNonEmpty();
     * => false
     * ```
     */
    public function isNonEmpty(): bool;
}
