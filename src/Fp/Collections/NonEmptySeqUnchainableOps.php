<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySeqUnchainableOps
{
    /**
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->at(1)->get()
     * => 2
     *
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->every(fn($elem) => $elem > 0)
     * => true
     * >>> NonEmptyLinkedList::collect([1, 2])->every(fn($elem) => $elem > 1)
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
     * >>> NonEmptyLinkedList::collect([new Foo(1), new Foo(2)])->everyOf(Foo::class)
     * => true
     * >>> NonEmptyLinkedList::collect([new Foo(1), new Bar(2)])->everyOf(Foo::class)
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
     * >>> NonEmptyLinkedList::collect([1, 2])->exists(fn($elem) => 2 === $elem)
     * => true
     * >>> NonEmptyLinkedList::collect([1, 2])->exists(fn($elem) => 3 === $elem)
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
     * >>> NonEmptyLinkedList::collect([1, new Foo(2)])->existsOf(Foo::class)
     * => true
     * >>> NonEmptyLinkedList::collect([1, new Foo(2)])->existsOf(Bar::class)
     * => false
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find first element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2, 3])->first(fn($elem) => $elem > 1)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get()
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
     * >>> NonEmptyLinkedList::collect([1, 2])->head()
     * => 1
     *
     * @psalm-return TV
     */
    public function head(): mixed;

    /**
     * Returns last collection element which satisfies the condition
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 0, 2])->last(fn($elem) => $elem > 0)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySeqOps::head}
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->firstElement()
     * => 1
     *
     * @psalm-return TV
     */
    public function firstElement(): mixed;

    /**
     * Returns last collection element
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->lastElement()
     * => 2
     *
     * @psalm-return TV
     */
    public function lastElement(): mixed;

    /**
     * Reduce multiple elements into one
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect(['1', '2'])->reduce(fn($acc, $cur) => $acc . $cur)
     * => '12'
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed;
}
