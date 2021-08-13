<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface NonEmptySeqOps
{
    /**
     * Add element to the collection end
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function append(mixed $elem): NonEmptySeq;

    /**
     * Add element to the collection start
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function prepend(mixed $elem): NonEmptySeq;

    /**
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * @psalm-return Option<TV>
     */
    public function at(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Filter collection by condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Seq<TV>
     */
    public function filter(callable $predicate): Seq;

    /**
     * Exclude null elements
     *
     * @psalm-return Seq<TV>
     */
    public function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Seq<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Seq;

    /**
     * Find first element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function first(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Seq<TVO>
     */
    public function flatMap(callable $callback): Seq;

    /**
     * Return first collection element
     *
     * @psalm-return TV
     */
    public function head(): mixed;

    /**
     * Returns last collection element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns last collection element
     *
     * @psalm-return TV
     */
    public function lastElement(): mixed;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptySeq<TVO>
     */
    public function map(callable $callback): NonEmptySeq;

    /**
     * Reduce multiple elements into one
     *
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return TV
     */
    public function reduce(callable $callback): mixed;

    /**
     * Copy collection in reversed order
     *
     * @psalm-return NonEmptySeq<TV>
     */
    public function reverse(): NonEmptySeq;

    /**
     * Returns every collection element except first
     *
     * @psalm-return Seq<TV>
     */
    public function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return NonEmptySeq<TV>
     */
    public function unique(callable $callback): NonEmptySeq;
}
