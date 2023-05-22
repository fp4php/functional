<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Functional\Separated\Separated;
use Fp\Operations\FoldOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\FoldMethodReturnTypeProvider;

/**
 * @template-covariant TK
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface MapTerminalOps
{
    /**
     * Get an element by its key
     * Alias for {@see MapOps::get}
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option;

    /**
     * Get an element by its key
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->every(fn($value) => $value > 0);
     * => true
     *
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->every(fn($value) => $value > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Same as {@see MapTerminalOps::every()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     */
    public function everyN(callable $predicate): bool;

    /**
     * Same as {@see MapTerminalOps::every()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function everyKV(callable $predicate): bool;

    /**
     * Returns true if some collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->exists(fn($value) => $value > 0);
     * => true
     *
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->exists(fn($value) => $value > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Same as {@see MapTerminalOps::exists()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     */
    public function existsN(callable $predicate): bool;

    /**
     * Same as {@see MapTerminalOps::exists()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function existsKV(callable $predicate): bool;

    /**
     * Suppose you have an HashMap<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<HashMap<TVO>>
     * i.e. an Some<HashMap<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> HashMap::collectPairs(['a' => 1, 'b' => 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(HashMap('a' -> 1, 'b' -> 2))
     *
     * >>> HashMap::collectPairs(['a' => 0, 'b' => 1])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<Map<TK, TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * Same as {@see MapTerminalOps::traverseOption()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template TVO
     *
     * @param callable(mixed...): Option<TVO> $callback
     * @return Option<Map<TK, TVO>>
     */
    public function traverseOptionN(callable $callback): Option;

    /**
     * Same as {@see MapTerminalOps::traverseOption()}, but passing also the key to the $predicate function.
     *
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $callback
     * @return Option<Map<TK, TVO>>
     */
    public function traverseOptionKV(callable $callback): Option;

    /**
     * Same as {@see MapTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> HashMap::collect([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(HashMap(0 -> 1, 1 -> 2, 2 -> 3))
     *
     * >>> HashMap::collect([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is Map<TK, Option<TVO>>
     *
     * @return Option<Map<TK, TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Suppose you have an Map<TK, TV> and you want to format each element with a function that returns an Either<E, TVO>.
     * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, Map<TK, TVO>>
     * i.e. an Right<Map<TK, TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
     *
     * ```php
     * >>> HashMap::collect(['fst' => 1, 'snd' => 2, 'thr' => 3])
     * >>>     ->traverseEither(fn($x) => $x >= 1
     * >>>         ? Either::right($x)
     * >>>         : Either::left('err'));
     * => Right(HashMap('fst' => 1, 'snd' => 2, 'thr' => 3))
     *
     * >>> HashMap::collect(['zro' => 0, 'fst' => 1, 'snd' => 2])
     * >>>     ->traverseEither(fn($x) => $x >= 1
     * >>>         ? Either::right($x)
     * >>>         : Either::left('err'));
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<E, TVO> $callback
     * @return Either<E, Map<TK, TVO>>
     */
    public function traverseEither(callable $callback): Either;

    /**
     * Same as {@see MapTerminalOps::traverseEither()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<E, TVO> $callback
     * @return Either<E, Map<TK, TVO>>
     */
    public function traverseEitherN(callable $callback): Either;

    /**
     * Same as {@see MapTerminalOps::traverseEither()}, but passing also the key to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(TK, TV): Either<E, TVO> $callback
     * @return Either<E, Map<TK, TVO>>
     */
    public function traverseEitherKV(callable $callback): Either;

    /**
     * Same as {@see MapTerminalOps::traverseEither()}, but collects all errors to non-empty-list.
     *
     * @template E
     * @template TVO
     *
     * @param callable(TV): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, Map<TK, TVO>>
     */
    public function traverseEitherMerged(callable $callback): Either;

    /**
     * Same as {@see MapTerminalOps::traverseEitherMerged()}, but passing also the key to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(TK, TV): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, Map<TK, TVO>>
     */
    public function traverseEitherMergedKV(callable $callback): Either;

    /**
     * Same as {@see MapTerminalOps::traverseEitherMerged()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template E
     * @template TVO
     *
     * @param callable(mixed...): Either<non-empty-list<E>, TVO> $callback
     * @return Either<non-empty-list<E>, Map<TK, TVO>>
     *
     * @see MapTapNMethodReturnTypeProvider
     */
    public function traverseEitherMergedN(callable $callback): Either;

    /**
     * Same as {@see MapTerminalOps::traverseEither()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> HashMap::collect([
     * >>>     'fst' => Either::right(1),
     * >>>     'snd' => Either::right(2),
     * >>>     'thr' => Either::right(3),
     * >>> ])->sequenceEither();
     * => Right(HashMap('fst' => 1, 'snd' => 2, 'thr' => 3))
     *
     * >>> HashMap::collect([
     * >>>     'fst' => Either::left('err'),
     * >>>     'snd' => Either::right(2),
     * >>>     'thr' => Either::right(3),
     * >>> ])->sequenceEither();
     * => Left('err')
     * ```
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is Map<TK, Either<E, TVO>>
     *
     * @return Either<E, Map<TK, TVO>>
     */
    public function sequenceEither(): Either;

    /**
     * Same as {@see Set::sequenceEither()} but merge all left errors into non-empty-list.
     *
     * @template E
     * @template TVO
     * @psalm-if-this-is Map<TK, Either<non-empty-list<E>, TVO>>
     *
     * @return Either<non-empty-list<E>, Map<TK, TVO>>
     */
    public function sequenceEitherMerged(): Either;

    /**
     * Split collection to two parts by predicate function.
     * If $predicate returns true then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> HashMap::collect(['k0' => 0, 'k1' => 1, 'k2' => 2, 'k3' => 3, 'k4' => 4, 'k5' => 5])
     * >>>     ->partition(fn($i) => $i < 3);
     * => Separated(HashMap('k3' => 3, 'k4' => 4, 'k5' => 5), HashMap('k0' => 0, 'k1' => 1, 'k2' => 2))
     * ```
     *
     * @param callable(TV): bool $predicate
     * @return Separated<Map<TK, TV>, Map<TK, TV>>
     */
    public function partition(callable $predicate): Separated;

    /**
     * Same as {@see MapTerminalOps::partition()}, but deconstruct input tuple and pass it to the $predicate function.
     *
     * @param callable(mixed...): bool $predicate
     * @return Separated<Map<TK, TV>, Map<TK, TV>>
     */
    public function partitionN(callable $predicate): Separated;

    /**
     * Same as {@see MapTerminalOps::partition()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     * @return Separated<Map<TK, TV>, Map<TK, TV>>
     */
    public function partitionKV(callable $predicate): Separated;

    /**
     * Similar to {@see MapTerminalOps::partition()} but uses {@see Either} instead of bool.
     * So the output types LO/RO can be different from the input type TV.
     * If $callback returns Right then item gonna to right.
     * Otherwise to left.
     *
     * ```php
     * >>> HashMap::collect(['k0' => 0, 'k1' => 1, 'k2' => 2, 'k3' => 3, 'k4' => 4, 'k5' => 5])
     * >>>     ->partitionMap(fn($i) => $i >= 5 ? Either::left("L:{$i}") : Either::right("R:{$i}"));
     * => Separated(HashMap('k5' => 'L:5'), HashMap('k0' => 'R:0', 'k1' => 'R:1', 'k2' => 'R:2', 'k3' => 'R:3', 'k4' => 'R:4'))
     * ```
     *
     * @template LO
     * @template RO
     *
     * @param callable(TV): Either<LO, RO> $callback
     * @return Separated<Map<TK, LO>, Map<TK, RO>>
     */
    public function partitionMap(callable $callback): Separated;

    /**
     * Same as {@see MapTerminalOps::partitionMap()}, but deconstruct input tuple and pass it to the $callback function.
     *
     * @template LO
     * @template RO
     *
     * @param callable(mixed...): Either<LO, RO> $callback
     * @return Separated<Map<TK, LO>, Map<TK, RO>>
     */
    public function partitionMapN(callable $callback): Separated;

    /**
     * Same as {@see MapTerminalOps::partitionMap()}, but passing also the key to the $callback function.
     *
     * @template LO
     * @template RO
     *
     * @param callable(TK, TV): Either<LO, RO> $callback
     * @return Separated<Map<TK, LO>, Map<TK, RO>>
     */
    public function partitionMapKV(callable $callback): Separated;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> HashMap::collect(['fst' => 1, 'snd' => 2, 'thr' => 3])->fold('0')(fn($acc, $cur) => $acc . $cur);
     * => '0123'
     * ```
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldOperation<TV, TVO>
     *
     * @see FoldMethodReturnTypeProvider
     */
    public function fold(mixed $init): FoldOperation;

    public function isEmpty(): bool;

    /**
     * Returns sequence of collection keys
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->keys(fn($elem) => $elem + 1)->toList();
     * => ['1', '2']
     * ```
     *
     * @return Seq<TK>
     */
    public function keys(): Seq;

    /**
     * Returns sequence of collection values
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->values(fn($elem) => $elem + 1)->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function values(): Seq;
}
