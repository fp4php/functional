<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-suppress InvalidTemplateParam
 * @template-covariant TV
 */
interface SeqChainableOps
{
    /**
     * Add element to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appended(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return Seq<TV|TVI>
     */
    public function appended(mixed $elem): Seq;

    /**
     * Add elements to the collection end
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->appendedAll([3, 4])->toList();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TVI
     *
     * @param Collection<TVI> | iterable<mixed, TVI> $suffix
     * @return Seq<TV|TVI>
     */
    public function appendedAll(iterable $suffix): Seq;

    /**
     * Add element to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prepended(0)->toList();
     * => [0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return Seq<TV|TVI>
     */
    public function prepended(mixed $elem): Seq;

    /**
     * Add elements to the collection start
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->prependedAll(-1, 0)->toList();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param Collection<TVI> | iterable<mixed, TVI> $prefix
     * @return Seq<TV|TVI>
     */
    public function prependedAll(iterable $prefix): Seq;

    /**
     * Filter collection by condition.
     * true - include element to new collection.
     * false - exclude element from new collection.
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->filter(fn($elem) => $elem > 1)->toList();
     * => [2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Seq<TV>
     */
    public function filter(callable $predicate): Seq;

    /**
     * Same as {@see SeqChainableOps::filter()}, but passing also the key to the $predicate function.
     *
     * @param callable(int, TV): bool $predicate
     * @return Seq<TV>
     */
    public function filterKV(callable $predicate): Seq;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, null])->filterNotNull()->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function filterNotNull(): Seq;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> LinkedList::collect([1, new Foo(2)])->filterOf(Foo::class)->toList();
     * => [Foo(2)]
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Seq<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Seq;

    /**
     * A combined {@see Seq::map} and {@see Seq::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> LinkedList::collect(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toList();
     * => [1, 2]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Seq<TVO>
     */
    public function filterMap(callable $callback): Seq;

    /**
     * Map collection and then flatten the result
     *
     * ```php
     * >>> LinkedList::collect([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): (Collection<TVO> | iterable<mixed, TVO>) $callback
     * @return Seq<TVO>
     */
    public function flatMap(callable $callback): Seq;

    /**
     * Produces a new collection of elements by mapping each element in collection
     * through a transformation function (callback)
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return Seq<TVO>
     */
    public function map(callable $callback): Seq;

    /**
     * Same as {@see SeqChainableOps::map()}, but passing also the key to the $callback function.
     *
     * ```php
     * >>> LinkedList::collect(['one', 'two'])
     * >>>     ->mapKV(fn($index, $elem) => "{$index}-{$elem}")
     * >>>     ->toList();
     * => ['1-one', '2-two']
     * ```
     *
     * @template TVO
     *
     * @param callable(int, TV): TVO $callback
     * @return Seq<TVO>
     */
    public function mapKV(callable $callback): Seq;

    /**
     * Copy collection in reversed order
     *
     * ```php
     * >>> LinkedList::collect([1, 2])->reverse()->toList();
     * => [2, 1]
     * ```
     *
     * @return Seq<TV>
     */
    public function reverse(): Seq;

    /**
     * Returns every collection element except first
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->tail()->toList();
     * => [2, 3]
     * ```
     *
     * @return Seq<TV>
     */
    public function tail(): Seq;

    /**
     * Returns collection unique elements
     *
     * ```php
     * >>> LinkedList::collect([1, 1, 2])->unique(fn($elem) => $elem)->toList();
     * => [1, 2]
     * ```
     *
     * @param callable(TV): (int|string) $callback
     * @return Seq<TV>
     */
    public function unique(callable $callback): Seq;

    /**
     * Take collection elements while predicate is true
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toList();
     * => [1, 2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Seq<TV>
     */
    public function takeWhile(callable $predicate): Seq;

    /**
     * Drop collection elements while predicate is true
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toList();
     * => [3]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Seq<TV>
     */
    public function dropWhile(callable $predicate): Seq;

    /**
     * Take N collection elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->take(2)->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function take(int $length): Seq;

    /**
     * Drop N collection elements
     *
     * ```php
     * >>> LinkedList::collect([1, 2, 3])->drop(2)->toList();
     * => [3]
     * ```
     *
     * @return Seq<TV>
     */
    public function drop(int $length): Seq;

    /**
     * Sort collection
     *
     * ```php
     * >>> LinkedList::collect([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toList();
     * => [1, 2, 3]
     *
     * >>> LinkedList::collect([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toList();
     * => [3, 2, 1]
     * ```
     *
     * @param callable(TV, TV): int $cmp
     * @return Seq<TV>
     */
    public function sorted(callable $cmp): Seq;

    /**
     * Call a function for every collection element
     *
     * ```php
     * >>> LinkedList::collect([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return Seq<TV>
     */
    public function tap(callable $callback): Seq;

    /**
     * Add specified separator between every pair of elements in the source collection.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->intersperse(0)->toList();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return Seq<TV|TVI>
     */
    public function intersperse(mixed $separator): Seq;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> ArrayList::collect([1, 2, 3])->zip([4, 5, 6, 7])->toList();
     * => [[1, 4], [2, 5], [3, 6]]
     * ```
     *
     * @template TVI
     *
     * @param Collection<TVI> | iterable<mixed, TVI> $that
     * @return Seq<array{TV, TVI}>
     */
    public function zip(iterable $that): Seq;
}
