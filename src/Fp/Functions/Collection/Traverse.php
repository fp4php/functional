<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Operations\TraverseEitherOperation;
use Fp\Operations\TraverseOptionOperation;
use function Fp\Callable\dropFirstArg;
use function Fp\Cast\asArray;

/**
 * Suppose you have an list<TV> and you want to format each element with a function that returns an Option<TVO>.
 * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<list<TVO>>
 * i.e. an Some<list<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
 *
 * @template TK of array-key
 * @template TVI
 * @template TVO
 *
 * @param iterable<TK, TVI> $collection
 * @param callable(TVI): Option<TVO> $callback
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
    return TraverseOptionOperation::of($collection)(dropFirstArg($callback))->map(asArray(...));
}

/**
 * Suppose you have an list<TV> and you want to format each element with a function that returns an Either<E, TVO>.
 * Using traverseEither you can apply $callback to all elements and directly obtain as a result an Either<E, list<TVO>>
 * i.e. an Right<list<TVO>> if all the results are Right<TVO>, or a Left<E> if at least one result is Left<E>.
 *
 * @template E
 * @template TK of array-key
 * @template TVI
 * @template TVO
 *
 * @param iterable<TK, TVI> $collection
 * @param callable(TVI): Either<E, TVO> $callback
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
    return TraverseEitherOperation::of($collection)(dropFirstArg($callback))->map(asArray(...));
}
