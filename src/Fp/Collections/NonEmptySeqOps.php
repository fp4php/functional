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
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->appended(3)->toArray()
     * => [1, 2, 3]
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function appended(mixed $elem): NonEmptySeq;

    /**
     * Add element to the collection start
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->prepended(0)->toArray()
     * => [0, 1, 2]
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return NonEmptySeq<TV|TVI>
     */
    public function prepended(mixed $elem): NonEmptySeq;

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
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->filter(fn($elem) => $elem > 1)->toArray()
     * => [2]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Seq<TV>
     */
    public function filter(callable $predicate): Seq;

    /**
     * Exclude null elements
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2, null])->filterNotNull()->toArray()
     * => [1, 2]
     *
     * @psalm-return Seq<TV>
     */
    public function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, new Foo(2)])->filterOf(Foo::class)->toArray()
     * => [Foo(2)]
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
     * REPL:
     * >>> NonEmptyLinkedList::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
     * => [1, 2, 3, 4, 5, 6]
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Seq<TVO>
     */
    public function flatMap(callable $callback): Seq;

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
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->map(fn($elem) => (string) $elem)->toArray()
     * => ['1', '2']
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return NonEmptySeq<TVO>
     */
    public function map(callable $callback): NonEmptySeq;

    /**
     * Reduce multiple elements into one
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect(['1', '2'])->reduce(fn($acc, $cur) => $acc . $cur)
     * => '12'
     *
     * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
     * @psalm-return TV
     */
    public function reduce(callable $callback): mixed;

    /**
     * Copy collection in reversed order
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2])->reverse()->toArray()
     * => [2, 1]
     *
     * @psalm-return NonEmptySeq<TV>
     */
    public function reverse(): NonEmptySeq;

    /**
     * Returns every collection element except first
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 2, 3])->tail()->toArray()
     * => [2, 3]
     *
     * @psalm-return Seq<TV>
     */
    public function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * REPL:
     * >>> NonEmptyLinkedList::collect([1, 1, 2])->unique(fn($elem) => $elem)->toArray()
     * => [1, 2]
     *
     * @psalm-param callable(TV): (int|string) $callback returns element unique id
     * @psalm-return NonEmptySeq<TV>
     */
    public function unique(callable $callback): NonEmptySeq;
}
