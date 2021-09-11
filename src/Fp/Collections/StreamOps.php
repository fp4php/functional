<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamOps
{
    /**
     * Add element to the stream end
     *
     * REPL:
     * >>> Stream::emits([1, 2])->appended(3)->toArray()
     * => [1, 2, 3]
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return Stream<TV|TVI>
     */
    public function appended(mixed $elem): Stream;

    /**
     * Add elements to the stream end
     *
     * REPL:
     * >>> Stream::emits([1, 2])->appendedAll([3, 4])->toArray()
     * => [1, 2, 3, 4]
     *
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @psalm-return Stream<TV|TVI>
     */
    public function appendedAll(iterable $suffix): Stream;

    /**
     * Add element to the stream start
     *
     * REPL:
     * >>> Stream::emits([1, 2])->prepended(0)->toArray()
     * => [0, 1, 2]
     *
     * @template TVI
     * @psalm-param TVI $elem
     * @psalm-return Stream<TV|TVI>
     */
    public function prepended(mixed $elem): Stream;

    /**
     * Add elements to the stream start
     *
     * REPL:
     * >>> Stream::emits([1, 2])->prependedAll(-1, 0)->toArray()
     * => [-1, 0, 1, 2]
     *
     * @template TVI
     * @psalm-param iterable<TVI> $prefix
     * @psalm-return Stream<TV|TVI>
     */
    public function prependedAll(iterable $prefix): Stream;

    /**
     * Returns true if every stream element satisfy the condition
     * and false otherwise
     *
     * REPL:
     * >>> Stream::emits([1, 2])->every(fn($elem) => $elem > 0)
     * => true
     * >>> Stream::emits([1, 2])->every(fn($elem) => $elem > 1)
     * => false
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Returns true if every stream element is of given class
     * false otherwise
     *
     * REPL:
     * >>> Stream::emits([new Foo(1), new Foo(2)])->everyOf(Foo::class)
     * => true
     * >>> Stream::emits([new Foo(1), new Bar(2)])->everyOf(Foo::class)
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
     * >>> Stream::emits([1, 2])->exists(fn($elem) => 2 === $elem)
     * => true
     * >>> Stream::emits([1, 2])->exists(fn($elem) => 3 === $elem)
     * => false
     *
     * @psalm-param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Returns true if there is stream element of given class
     * False otherwise
     *
     * REPL:
     * >>> Stream::emits([1, new Foo(2)])->existsOf(Foo::class)
     * => true
     * >>> Stream::emits([1, new Foo(2)])->existsOf(Bar::class)
     * => false
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     */
    public function existsOf(string $fqcn, bool $invariant = false): bool;

    /**
     * Filter stream by condition.
     * true - include element to new stream.
     * false - exclude element from new stream.
     *
     * REPL:
     * >>> Stream::emits([1, 2])->filter(fn($elem) => $elem > 1)->toArray()
     * => [2]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Stream<TV>
     */
    public function filter(callable $predicate): Stream;

    /**
     * Exclude null elements
     *
     * REPL:
     * >>> Stream::emits([1, 2, null])->filterNotNull()->toArray()
     * => [1, 2]
     *
     * @psalm-return Stream<TV>
     */
    public function filterNotNull(): Stream;

    /**
     * Filter elements of given class
     *
     * REPL:
     * >>> Stream::emits([1, new Foo(2)])->filterOf(Foo::class)->toArray()
     * => [Foo(2)]
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Stream<TVO>
     */
    public function filterOf(string $fqcn, bool $invariant = false): Stream;

    /**
     * A combined {@see Seq::map} and {@see Seq::filter}.
     *
     * Filtering is handled via Option instead of Boolean.
     * So the output type TVO can be different from the input type TV.
     *
     * REPL:
     * >>> Stream::emits(['zero', '1', '2'])
     * >>>     ->filterMap(fn($elem) => is_numeric($elem) ? Option::some((int) $elem) : Option::none())
     * >>>     ->toArray()
     * => [1, 2]
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): Option<TVO> $callback
     * @psalm-return Stream<TVO>
     */
    public function filterMap(callable $callback): Stream;

    /**
     * Find first element which satisfies the condition
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->first(fn($elem) => $elem > 1)->get()
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
     * >>> Stream::emits([new Bar(1), new Foo(2), new Foo(3)])->firstOf(Foo::class)->get()
     * => Foo(2)
     *
     * @psalm-template TVO
     * @psalm-param class-string<TVO> $fqcn fully qualified class name
     * @psalm-param bool $invariant if turned on then subclasses are not allowed
     * @psalm-return Option<TVO>
     */
    public function firstOf(string $fqcn, bool $invariant = false): Option;

    /**
     * Map stream and then flatten the result
     *
     * REPL:
     * >>> Stream::emits([2, 5])->flatMap(fn($e) => [$e - 1, $e, $e + 1])->toArray()
     * => [1, 2, 3, 4, 5, 6]
     *
     * @psalm-template TVO
     * @psalm-param callable(TV): iterable<TVO> $callback
     * @psalm-return Stream<TVO>
     */
    public function flatMap(callable $callback): Stream;

    /**
     * Fold many elements into one
     *
     * REPL:
     * >>> Stream::emits(['1', '2'])->fold('0', fn($acc, $cur) => $acc . $cur)
     * => '012'
     *
     * @template TA
     * @psalm-param TA $init initial accumulator value
     * @psalm-param callable(TA, TV): TA $callback (accumulator, current element): new accumulator
     * @psalm-return TA
     */
    public function fold(mixed $init, callable $callback): mixed;

    /**
     * Reduce multiple elements into one
     * Returns None for empty stream
     *
     * REPL:
     * >>> Stream::emits(['1', '2'])->reduce(fn($acc, $cur) => $acc . $cur)->get()
     * => '12'
     *
     * @template TA
     * @psalm-param callable(TV|TA, TV): (TV|TA) $callback (accumulator, current value): new accumulator
     * @psalm-return Option<TV|TA>
     */
    public function reduce(callable $callback): Option;

    /**
     * Return first stream element
     *
     * REPL:
     * >>> Stream::emits([1, 2])->head()->get()
     * => 1
     *
     * @psalm-return Option<TV>
     */
    public function head(): Option;

    /**
     * Returns last stream element which satisfies the condition
     *
     * REPL:
     * >>> Stream::emits([1, 0, 2])->last(fn($elem) => $elem > 0)->get()
     * => 2
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Option<TV>
     */
    public function last(callable $predicate): Option;

    /**
     * Returns first stream element
     * Alias for {@see SeqOps::head}
     *
     * REPL:
     * >>> Stream::emits([1, 2])->firstElement()->get()
     * => 1
     *
     * @psalm-return Option<TV>
     */
    public function firstElement(): Option;

    /**
     * Returns last stream element
     *
     * REPL:
     * >>> Stream::emits([1, 2])->lastElement()->get()
     * => 2
     *
     * @psalm-return Option<TV>
     */
    public function lastElement(): Option;

    /**
     * Produces a new stream of elements by mapping each element in stream
     * through a transformation function (callback)
     *
     * REPL:
     * >>> Stream::emits([1, 2])->map(fn($elem) => (string) $elem)->toArray()
     * => ['1', '2']
     *
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return Stream<TVO>
     */
    public function map(callable $callback): Stream;

    /**
     * Returns every stream element except first
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->tail()->toArray()
     * => [2, 3]
     *
     * @psalm-return Stream<TV>
     */
    public function tail(): Stream;

    /**
     * Take stream elements while predicate is true
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->takeWhile(fn($e) => $e < 3)->toArray()
     * => [1, 2]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Stream<TV>
     */
    public function takeWhile(callable $predicate): Stream;

    /**
     * Drop stream elements while predicate is true
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->dropWhile(fn($e) => $e < 3)->toArray()
     * => [3]
     *
     * @psalm-param callable(TV): bool $predicate
     * @psalm-return Stream<TV>
     */
    public function dropWhile(callable $predicate): Stream;

    /**
     * Take N stream elements
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->take(2)->toArray()
     * => [1, 2]
     *
     * @psalm-return Stream<TV>
     */
    public function take(int $length): Stream;

    /**
     * Drop N stream elements
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->drop(2)->toArray()
     * => [3]
     *
     * @psalm-return Stream<TV>
     */
    public function drop(int $length): Stream;

    /**
     * Call a function for every stream element
     *
     * REPL:
     * >>> Stream::emits([new Foo(1), new Foo(2)])
     * >>>     ->tap(fn(Foo $foo) => $foo->a = $foo->a + 1)
     * >>>     ->map(fn(Foo $foo) => $foo->a)
     * >>>     ->toArray()
     * => [2, 3]
     *
     * @param callable(TV): void $callback
     * @psalm-return Stream<TV>
     */
    public function tap(callable $callback): Stream;

    /**
     * Emits the specified separator between every pair of elements in the source stream.
     *
     * REPL:
     * >>> Stream::emits([1, 2, 3])->intersperse(0)->toArray()
     * => [1, 0, 2, 0, 3]
     *
     * @template TVI
     * @param TVI $separator
     * @psalm-return Stream<TV|TVI>
     */
    public function intersperse(mixed $separator): Stream;

    /**
     * Writes this stream to the stdout synchronously
     *
     * REPL:
     * >>> Stream::emits([1, 2])->lines()
     * 1
     * 2
     *
     * @psalm-return Stream<TV>
     */
    public function lines(): Stream;

    /**
     * Run stream without care of the output
     *
     * REPL:
     * >>> Stream::drain([1, 2])->drain()
     */
    public function drain(): void;
}
