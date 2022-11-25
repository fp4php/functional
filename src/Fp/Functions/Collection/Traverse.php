<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Operations\TraverseEitherAccOperation;
use Fp\Operations\TraverseEitherMergeOperation;
use Fp\Operations\TraverseEitherOperation;
use Fp\Operations\TraverseOptionOperation;

use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asArray;
use function Fp\Cast\asList;

/**
 * Suppose you have a list<TV> and you want to format each element with a function that returns an Option<TVO>.
 * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<list<TVO>>
 * i.e. an Some<list<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
 *
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): Option<TVO> $callback
 * @return Option<array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Option<non-empty-list<TVO>>      :
 *    $collection is list            ? Option<list<TVO>>                :
 *    $collection is non-empty-array ? Option<non-empty-array<TK, TVO>> :
 *    Option<array<TK, TVO>>
 * )
 */
function traverseOption(iterable $collection, callable $callback): Option
{
    return traverseOptionKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see traverseOption()}, but passing also the key to the $callback function.
 *
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): Option<TVO> $callback
 * @return Option<array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Option<non-empty-list<TVO>>      :
 *    $collection is list            ? Option<list<TVO>>                :
 *    $collection is non-empty-array ? Option<non-empty-array<TK, TVO>> :
 *    Option<array<TK, TVO>>
 * )
 */
function traverseOptionKV(iterable $collection, callable $callback): Option
{
    return TraverseOptionOperation::of($collection)($callback)->map(asArray(...));
}

/**
 * Suppose you have a list<TV> and you want to format each element with a function that returns an Either<E, TVO>.
 * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, list<TVO>>
 * i.e. an Right<list<TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
 *
 * @template E
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): Either<E, TVO> $callback
 * @return Either<E, array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<E, non-empty-list<TVO>>      :
 *    $collection is list            ? Either<E, list<TVO>>                :
 *    $collection is non-empty-array ? Either<E, non-empty-array<TK, TVO>> :
 *    Either<E, array<TK, TVO>>
 * )
 */
function traverseEither(iterable $collection, callable $callback): Either
{
    return traverseEitherKV($collection, dropFirstArg($callback));
}

/**
 * Same as {@see traverseEither()}, but passing also the key to the $callback function.
 *
 * @template E
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): Either<E, TVO> $callback
 * @return Either<E, array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<E, non-empty-list<TVO>>      :
 *    $collection is list            ? Either<E, list<TVO>>                :
 *    $collection is non-empty-array ? Either<E, non-empty-array<TK, TVO>> :
 *    Either<E, array<TK, TVO>>
 * )
 */
function traverseEitherKV(iterable $collection, callable $callback): Either
{
    return TraverseEitherOperation::of($collection)($callback)->map(asArray(...));
}

/**
 * Similar to {@see traverseEither} but collects all errors to non-empty-list.
 *
 * @template E
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): Either<non-empty-list<E>, TVO> $callback
 * @return Either<non-empty-list<E>, array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<non-empty-list<E>, non-empty-list<TVO>>      :
 *    $collection is list            ? Either<non-empty-list<E>, list<TVO>>                :
 *    $collection is non-empty-array ? Either<non-empty-list<E>, non-empty-array<TK, TVO>> :
 *    Either<non-empty-list<E>, array<TK, TVO>>
 * )
 */
function traverseEitherMerge(iterable $collection, callable $callback): Either
{
    return traverseEitherKVMerge($collection, dropFirstArg($callback));
}

/**
 * Same as {@see traverseEitherMerge()}, but passing also the key to the $callback function.
 *
 * @template E
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): Either<non-empty-list<E>, TVO> $callback
 * @return Either<non-empty-list<E>, array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<non-empty-list<E>, non-empty-list<TVO>>      :
 *    $collection is list            ? Either<non-empty-list<E>, list<TVO>>                :
 *    $collection is non-empty-array ? Either<non-empty-list<E>, non-empty-array<TK, TVO>> :
 *    Either<non-empty-list<E>, array<TK, TVO>>
 * )
 */
function traverseEitherKVMerge(iterable $collection, callable $callback): Either
{
    return TraverseEitherMergeOperation::of($collection)($callback)->map(asArray(...));
}

/**
 * Same as {@see traverseEither()} but accumulates all left errors.
 *
 * @template E
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): Either<E, TVO> $callback
 * @return Either<E, array<TK, TVO>>
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<non-empty-list<E>, non-empty-list<TVO>>      :
 *    $collection is list            ? Either<non-empty-list<E>, list<TVO>>                :
 *    $collection is non-empty-array ? Either<non-empty-array<TK, E>, non-empty-array<TK, TVO>> :
 *    Either<non-empty-array<TK, E>, array<TK, TVO>>
 * )
 */
function traverseEitherAcc(iterable $collection, callable $callback): Either
{
    return traverseEitherKVAcc($collection, dropFirstArg($callback));
}

/**
 * Same as {@see traverseEitherKV()} but accumulates all left errors.
 *
 * @template E
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TK, TV): Either<E, TVO> $callback
 * @return Either<E, array<TK, TVO>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<non-empty-list<E>, non-empty-list<TVO>>      :
 *    $collection is list            ? Either<non-empty-list<E>, list<TVO>>                :
 *    $collection is non-empty-array ? Either<non-empty-array<TK, E>, non-empty-array<TK, TVO>> :
 *    Either<non-empty-array<TK, E>, array<TK, TVO>>
 * )
 */
function traverseEitherKVAcc(iterable $collection, callable $callback): Either
{
    return TraverseEitherAccOperation::of($collection)($callback)
        ->mapLeft(function($gen) use ($collection) {
            /** @var non-empty-array<TK, E> */
            return is_array($collection) && array_is_list($collection)
                ? asList($gen)
                : asArray($gen);
        })
        ->map(asArray(...));
}
