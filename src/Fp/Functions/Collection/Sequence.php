<?php

declare(strict_types=1);

namespace Fp\Collection;

use Closure;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Operations\TraverseEitherAccOperation;
use Fp\Operations\TraverseEitherMergedOperation;
use Fp\Operations\TraverseEitherOperation;
use Fp\Operations\TraverseOptionOperation;

use function Fp\Cast\asArray;
use function Fp\Cast\asList;

/**
 * Same as {@see traverseOption()} but use {@see id()} implicitly for $callback.
 *
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Option<TVI> | Closure(): Option<TVI>> $collection
 * @return Option<array<TK, TVI>>
 * @psalm-return (
 *    $collection is non-empty-list  ? Option<non-empty-list<TVI>>      :
 *    $collection is list            ? Option<list<TVI>>                :
 *    $collection is non-empty-array ? Option<non-empty-array<TK, TVI>> :
 *    Option<array<TK, TVI>>
 * )
 */
function sequenceOption(iterable $collection): Option
{
    return TraverseOptionOperation::id($collection)->map(asArray(...));
}

/**
 * Varargs version of {@see sequenceOption()}.
 *
 * @template TVI
 *
 * @param (Option<TVI> | Closure(): Option<TVI>) ...$items
 * @return Option<list<TVI>>
 *
 * @no-named-arguments
 */
function sequenceOptionT(Option|Closure ...$items): Option
{
    return TraverseOptionOperation::id($items)->map(asList(...));
}

/**
 * Same as {@see traverseEither()} but use {@see id()} implicitly for $callback.
 *
 * @template E
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Either<E, TVI> | Closure(): Either<E, TVI>> $collection
 * @return Either<E, array<TK, TVI>>
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<E, non-empty-list<TVI>>      :
 *    $collection is list            ? Either<E, list<TVI>>                :
 *    $collection is non-empty-array ? Either<E, non-empty-array<TK, TVI>> :
 *    Either<E, array<TK, TVI>>
 * )
 */
function sequenceEither(iterable $collection): Either
{
    return TraverseEitherOperation::id($collection)->map(asArray(...));
}

/**
 * Same as {@see traverseEither()} but merge all left errors into non-empty-list.
 *
 * @template E
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Either<non-empty-list<E>, TVI> | Closure(): Either<non-empty-list<E>, TVI>> $collection
 * @return Either<non-empty-list<E>, array<TK, TVI>>
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<non-empty-list<E>, non-empty-list<TVI>>      :
 *    $collection is list            ? Either<non-empty-list<E>, list<TVI>>                :
 *    $collection is non-empty-array ? Either<non-empty-list<E>, non-empty-array<TK, TVI>> :
 *    Either<non-empty-list<E>, array<TK, TVI>>
 * )
 */
function sequenceEitherMerged(iterable $collection): Either
{
    return TraverseEitherMergedOperation::id($collection)->map(asArray(...));
}

/**
 * Varargs version of {@see sequenceEitherMerged()}.
 *
 * @template E
 * @template TK of array-key
 * @template TVI
 *
 * @param Either<non-empty-list<E>, TVI> | Closure(): Either<non-empty-list<E>, TVI> ...$items
 * @return Either<non-empty-list<E>, list<TVI>>
 */
function sequenceEitherMergedT(Either|Closure ...$items): Either
{
    return TraverseEitherMergedOperation::id($items)->map(asList(...));
}

/**
 * Varargs version of {@see sequenceEither()}.
 *
 * @template E
 * @template TVI
 *
 * @param Either<E, TVI> | Closure(): Either<E, TVI> ...$items
 * @return Either<E, list<TVI>>
 */
function sequenceEitherT(Either|Closure ...$items): Either
{
    return TraverseEitherOperation::id($items)->map(asList(...));
}

/**
 * Same as {@see sequenceEither()} but accumulates all left errors.
 *
 * @template E
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Either<E, TVI> | Closure(): Either<E, TVI>> $collection
 * @return Either<non-empty-array<TK, E>, array<TK, TVI>>
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<non-empty-list<E>, non-empty-list<TVI>>           :
 *    $collection is list            ? Either<non-empty-list<E>, list<TVI>>                     :
 *    $collection is non-empty-array ? Either<non-empty-array<TK, E>, non-empty-array<TK, TVI>> :
 *    Either<non-empty-array<TK, E>, array<TK, TVI>>
 * )
 */
function sequenceEitherAcc(iterable $collection): Either
{
    $isList = is_array($collection) && array_is_list($collection);

    return TraverseEitherAccOperation::id($collection)
        ->mapLeft(function($gen) use ($collection, $isList) {
            /** @var non-empty-array<TK, E> */
            return $isList ? asList($gen) : asArray($gen);
        })
        ->map(asArray(...));
}
