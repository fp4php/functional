<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface NonEmptySeqTerminalOps
{
    /**
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->at(1)->get();
     * => 2
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option;

    /**
     * Alias for {@see NonEmptySeqTerminalOps::at()}
     *
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])(1)->get();
     * => 2
     * ```
     *
     * @psalm-return Option<TV>
     */
    public function __invoke(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->every(fn($elem) => $elem > 0);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->every(fn($elem) => $elem > 1);
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
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Foo(2)])->everyOf(Foo::class);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([new Foo(1), new Bar(2)])->everyOf(Foo::class);
     * => false
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Suppose you have an NonEmptyArrayList<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<NonEmptyArrayList<TVO>>
     * i.e. an Some<NonEmptyArrayList<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> NonEmptyArrayList::collect([1, 2, 3])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(NonEmptyArrayList(1, 2, 3))
     *
     * >>> NonEmptyArrayList::collect([0, 1, 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<NonEmptySeq<TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * Find if there is element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->exists(fn($elem) => 2 === $elem);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->exists(fn($elem) => 3 === $elem);
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
     * >>> NonEmptyLinkedList::collectNonEmpty([1, new Foo(2)])->existsOf(Foo::class);
     * => true
     *
     * >>> NonEmptyLinkedList::collectNonEmpty([1, new Foo(2)])->existsOf(Bar::class);
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
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2, 3])->first(fn($elem) => $elem > 1)->get();
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
     * >>> NonEmptyLinkedList::collectNonEmpty([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get();
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
     * Find last element of given class
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([
     *     new Foo(1),
     *     new Bar(1),
     *     new Foo(2)
     * ])->lastOf(Foo::class)->get();
     * => Foo(2)
     * ```
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function lastOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Return first collection element
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->head();
     * => 1
     * ```
     *
     * @psalm-return TV
     */
    public function head(): mixed;

    /**
     * Returns last collection element which satisfies the condition
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 0, 2])->last(fn($elem) => $elem > 0)->get();
     * => 2
     * ```
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first collection element
     * Alias for {@see NonEmptySeqOps::head}
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->firstElement();
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
     * >>> NonEmptyLinkedList::collectNonEmpty([1, 2])->lastElement();
     * => 2
     * ```
     *
     * @psalm-return TV
     */
    public function lastElement(): mixed;

    /**
     * Reduce multiple elements into one
     *
     * ```php
     * >>> NonEmptyLinkedList::collectNonEmpty(['1', '2'])->reduce(fn($acc, $cur) => $acc . $cur);
     * => '12'
     * ```
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return (TV|TA)
     */
    public function reduce(callable $callback): mixed;
}
