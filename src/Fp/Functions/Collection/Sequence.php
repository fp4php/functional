<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Fp\Operations\TraverseEitherOperation;
use Fp\Operations\TraverseOptionOperation;

use function Fp\Cast\asArray;

/**
 * Same as {@see traverseOption()} but use {@see id()} implicitly for $callback.
 *
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Option<TVI>> $collection
 * @return Option<array<TK, TVI>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Option<non-empty-list<TVI>>      :
 *    $collection is list            ? Option<list<TVI>>                :
 *    $collection is non-empty-array ? Option<non-empty-array<TK, TVI>> :
 *    Option<array<TK, TVI>>
 * )
 */
function sequenceOption(iterable $collection): Option
{
    return TraverseOptionOperation::id($collection)->map(fn($gen) => asArray($gen));
}

/**
 * Same as {@see traverseEither()} but use {@see id()} implicitly for $callback.
 *
 * @template E
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Either<E, TVI>> $collection
 * @return Either<E, array<TK, TVI>>
 *
 * @psalm-return (
 *    $collection is non-empty-list  ? Either<E, non-empty-list<TVI>>      :
 *    $collection is list            ? Either<E, list<TVI>>                :
 *    $collection is non-empty-array ? Either<E, non-empty-array<TK, TVI>> :
 *    Either<E, array<TK, TVI>>
 * )
 */
function sequenceEither(iterable $collection): Either
{
    return TraverseEitherOperation::id($collection)->map(fn($gen) => asArray($gen));
}
