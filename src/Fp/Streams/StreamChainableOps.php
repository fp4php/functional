<?php

declare(strict_types=1);

namespace Fp\Streams;

use Fp\Collections\Collection;
use Fp\Collections\NonEmptyCollection;
use Fp\Collections\Seq;
use Fp\Functional\Option\Option;

/**
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface StreamChainableOps
{
    /**
     * Add element to the stream end
     *
     * ```php
     * >>> Stream::emits([1, 2])->appended(3)->toList();
     * => [1, 2, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return Stream<TV|TVI>
     */
    public function appended(mixed $elem): Stream;

    /**
     * Add elements to the stream end
     *
     * ```php
     * >>> Stream::emits([1, 2])->appendedAll([3, 4])->toList();
     * => [1, 2, 3, 4]
     * ```
     *
     * @template TVI
     *
     * @param NonEmptyCollection<TVI> | Collection<TVI> | iterable<mixed, TVI> $suffix
     * @return Stream<TV|TVI>
     */
    public function appendedAll(iterable $suffix): Stream;

    /**
     * Add element to the stream start
     *
     * ```php
     * >>> Stream::emits([1, 2])->prepended(0)->toList();
     * => [0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param TVI $elem
     * @return Stream<TV|TVI>
     */
    public function prepended(mixed $elem): Stream;

    /**
     * Add elements to the stream start
     *
     * ```php
     * >>> Stream::emits([1, 2])->prependedAll(-1, 0)->toList();
     * => [-1, 0, 1, 2]
     * ```
     *
     * @template TVI
     *
     * @param NonEmptyCollection<TVI> | Collection<TVI> | iterable<mixed, TVI> $prefix
     * @return Stream<TV|TVI>
     */
    public function prependedAll(iterable $prefix): Stream;

    /**
     * Filter stream by condition.
     * true - include element to new stream.
     * false - exclude element from new stream.
     *
     * ```php
     * >>> Stream::emits([1, 2])->filter(fn($elem) => $elem > 1)->toList();
     * => [2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Stream<TV>
     */
    public function filter(callable $predicate): Stream;

    /**
     * Exclude null elements
     *
     * ```php
     * >>> Stream::emits([1, 2, null])->filterNotNull()->toList();
     * => [1, 2]
     * ```
     *
     * @return Stream<TV>
     */
    public function filterNotNull(): Stream;

    /**
     * Filter elements of given class
     *
     * ```php
     * >>> Stream::emits([1, new Foo(2)])->filterOf(Foo::class)->toList();
     * => [Foo(2)]
     * ```
     *
     * @template TVO
     *
     * @param class-string<TVO> $fqcn
     * @param bool $invariant
     * @return Stream<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Stream;

    /**
     * A combined {@see Stream::map} and {@see Stream::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * ```php
     * >>> Stream::emits(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toList();
     * => [1, 2]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Stream<TVO>
     */
    public function filterMap(callable $callback): Stream;

    /**
     * Map stream and then flatten the result
     *
     * ```php
     * >>> Stream::emits([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toList();
     * => [1, 2, 3, 4, 5, 6]
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): (NonEmptyCollection<TVO> | Collection<TVO> | iterable<mixed, TVO>) $callback
     * @return Stream<TVO>
     */
    public function flatMap(callable $callback): Stream;

    /**
     * Produces a new stream of elements by mapping each element in stream
     * through a transformation function (callback)
     *
     * ```php
     * >>> Stream::emits([1, 2])->map(fn($elem) => (string) $elem)->toList();
     * => ['1', '2']
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): TVO $callback
     * @return Stream<TVO>
     */
    public function map(callable $callback): Stream;

    /**
     * Returns every stream element except first
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->tail()->toList();
     * => [2, 3]
     * ```
     *
     * @return Stream<TV>
     */
    public function tail(): Stream;

    /**
     * Take stream elements while predicate is true
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toList();
     * => [1, 2]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Stream<TV>
     */
    public function takeWhile(callable $predicate): Stream;

    /**
     * Drop stream elements while predicate is true
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toList();
     * => [3]
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Stream<TV>
     */
    public function dropWhile(callable $predicate): Stream;

    /**
     * Take N stream elements
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->take(2)->toList();
     * => [1, 2]
     * ```
     *
     * @return Stream<TV>
     */
    public function take(int $length): Stream;

    /**
     * Drop N stream elements
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->drop(2)->toList();
     * => [3]
     * ```
     *
     * @return Stream<TV>
     */
    public function drop(int $length): Stream;

    /**
     * Call a function for every stream element
     *
     * ```php
     * >>> Stream::emits([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toList();
     * => [2, 3]
     * ```
     *
     * @param callable(TV): void $callback
     * @return Stream<TV>
     */
    public function tap(callable $callback): Stream;

    /**
     * Emits the specified separator between every pair of elements in the source stream.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->intersperse(0)->toList();
     * => [1, 0, 2, 0, 3]
     * ```
     *
     * @template TVI
     *
     * @param TVI $separator
     * @return Stream<TV|TVI>
     */
    public function intersperse(mixed $separator): Stream;

    /**
     * Writes this stream to the stdout synchronously
     *
     * ```php
     * >>> Stream::emits([1, 2])->lines()->drain();
     * 1
     * 2
     * ```
     *
     * @return Stream<TV>
     */
    public function lines(): Stream;

    /**
     * Deterministically zips elements, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->zip(Stream::emits([4, 5, 6, 7]))->toList();
     * => [[1, 4], [2, 5], [3, 6]]
     * ```
     *
     * @template TVI
     *
     * @param NonEmptyCollection<TVI> | Collection<TVI> | iterable<mixed, TVI> $that
     * @return Stream<array{TV, TVI}>
     */
    public function zip(iterable $that): Stream;

    /**
     * Deterministically interleaves elements, starting on the left, terminating when the end of either branch is reached naturally.
     *
     * ```php
     * >>> Stream::emits([1, 2, 3])->interleave(Stream::emits([4, 5, 6, 7]))->toList();
     * => [1, 4, 2, 5, 3, 6]
     * ```
     *
     * @template TVI
     *
     * @param NonEmptyCollection<TVI> | Collection<TVI> | iterable<mixed, TVI> $that
     * @return Stream<TV|TVI>
     */
    public function interleave(iterable $that): Stream;

    /**
     * Produce stream of chunks with given size from this stream
     *
     * ```php
     * >>> Stream::emits([1, 2, 3, 4, 5])->chunks(2);
     * => Stream(Seq(1, 2), Seq(3, 4), Seq(5))
     * ```
     *
     * @param positive-int $size
     * @return Stream<Seq<TV>>
     */
    public function chunks(int $size): Stream;

    /**
     * Partitions the input into a stream of chunks according to a discriminator function.
     *
     * ```php
     * >>> Stream::emits(["Hello", "Hi", "Greetings", "Hey"])
     * >>>     ->groupAdjacentBy(fn($str) => $str[0]);
     * => Stream(
     * =>     ["H", Seq("Hello", "Hi")],
     * =>     ["G", Seq("Greetings")],
     * =>     ["H", Seq("Hey")]
     * => )
     * ```
     *
     * @template D
     *
     * @param callable(TV): D $discriminator
     * @return Stream<array{D, Seq<TV>}>
     */
    public function groupAdjacentBy(callable $discriminator): Stream;

    /**
     * Sort streamed elements
     *
     * ```php
     * >>> Stream::emits([2, 1, 3])->sorted(fn($lhs, $rhs) => $lhs - $rhs)->toList();
     * => [1, 2, 3]
     *
     * >>> Stream::emits([2, 1, 3])->sorted(fn($lhs, $rhs) => $rhs - $lhs)->toList();
     * => [3, 2, 1]
     * ```
     *
     * @param callable(TV, TV): int $cmp
     * @return Stream<TV>
     */
    public function sorted(callable $cmp): Stream;
}
