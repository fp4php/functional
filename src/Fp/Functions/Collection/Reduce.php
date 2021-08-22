<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Functional\Option\Option;

use function Fp\Cast\asNonEmptyList;

/**
 * Reduce multiple elements into one
 * Returns None for empty collection
 *
 * REPL:
 * >>> reduce(
 *     ['a', 'b', 'c'],
 *     fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
 * )->get();
 * => 'abc'
 *
 * @deprecated use {@see Seq::reduce()} or {@see Set::reduce()}
 * @psalm-suppress DeprecatedFunction
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
 *
 * @psalm-return Option<TV>
 */
function reduce(iterable $collection, callable $callback): Option
{
    return Option::do(function () use ($collection, $callback) {
        $nel = yield asNonEmptyList($collection);
        return reduceNel($nel, $callback);
    });
}

/**
 * Reduce non-empty-list into one value
 *
 * REPL:
 * >>> reduceNel(
 *     ['a', 'b', 'c'],
 *     fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
 * );
 * => 'abc'
 *
 * @deprecated use {@see NonEmptySeq::reduce()} or {@see NonEmptySet::reduce()}
 * @psalm-suppress DeprecatedFunction
 * @psalm-template TV
 *
 * @psalm-param non-empty-list<TV> $collection
 * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
 *
 * @psalm-return TV
 */
function reduceNel(array $collection, callable $callback): mixed
{
    return reduceNer($collection, $callback);
}

/**
 * Reduce non-empty-array into one value
 *
 * REPL:
 * >>> reduceNer(
 *     ['x' => 'a', 'b', 'c'],
 *     fn(string $accumulator, string $currentValue) => $accumulator . $currentValue
 * );
 * => 'abc'
 *
 *
 * @deprecated use {@see NonEmptySeq::reduce()} or {@see NonEmptySet::reduce()}
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param non-empty-array<TK, TV> $collection
 * @psalm-param callable(TV, TV): TV $callback (accumulator, current value): new accumulator
 *
 * @psalm-return TV
 */
function reduceNer(array $collection, callable $callback): mixed
{
    $acc = array_shift($collection);

    foreach ($collection as $element) {
        $acc = call_user_func($callback, $acc, $element);
    }

    return $acc;
}
