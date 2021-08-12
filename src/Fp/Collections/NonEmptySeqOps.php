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
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    function append(mixed $elem): NonEmptySeq;

    /**
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    function prepend(mixed $elem): NonEmptySeq;

    /**
     * Returns true if there is collection element of given class
     * False otherwise
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    function anyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find element by its index
     * Returns None if there is no such collection element
     *
     * @psalm-return Option<TV>
     */
    function at(int $index): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * @psalm-param callable(TV): bool $predicate
     */
    function every(callable $predicate): bool;

    /**
     * Returns true if every collection element is of given class
     * false otherwise
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    function everyOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Find if there is element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     */
    function exists(callable $predicate): bool;

    /**
     * Filter collection by condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Seq<TV>
     */
    function filter(callable $predicate): Seq;

    /**
     * Filter not null elements
     *
     * @psalm-return Seq<TV>
     */
    function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Seq<TVO>
     */
    function filterOf(string $fqcn, bool $invariant = false): Seq;

    /**
     * Find first element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    function first(callable $predicate): Option;

    /**
     * Find first element of given class
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Seq<TVO>
     */
    function flatMap(callable $callback): Seq;

    /**
     * Do something for all collection elements
     *
     * @psalm-param callable(TV) $callback
     */
    function forAll(callable $callback): void;

    /**
     * @psalm-return TV
     */
    function head(): mixed;

    /**
     * Returns last collection element which satisfies the condition
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    function last(callable $predicate): Option;

    /**
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
    function reduce(callable $callback): mixed;

    /**
     * Copy collection in reversed order
     *
     * @psalm-return NonEmptySeq<TV>
     */
    function reverse(): NonEmptySeq;

    /**
     * Returns every collection element except first
     *
     * @psalm-return Seq<TV>
     */
    function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return NonEmptySeq<TV>
     */
    function unique(callable $callback): NonEmptySeq;
}
