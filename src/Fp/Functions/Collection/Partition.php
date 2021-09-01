<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\Hooks\PartitionFunctionReturnTypeProvider;
use Fp\Psalm\Hooks\PartitionOfFunctionReturnTypeProvider;

use function Fp\of;

/**
 * Divide collection by given conditions
 *
 *
 * REPL:
 * >>> partition(
 *     ['a' => 1, 'b' => 2],
 *     fn(int $x) => $x % 2 === 0
 * );
 * => [[2], [1]]
 *
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool ...$predicates
 *
 * @see PartitionFunctionReturnTypeProvider
 */
function partition(iterable $collection, callable ...$predicates): array
{
    $predicateCount = count($predicates);
    $partitions = array_fill(0, $predicateCount + 1, []);

    foreach ($collection as $index => $element) {
        foreach ($predicates as $partition => $callback) {
            if ($callback($element, $index)) {
                $partitions[$partition][] = $element;
                continue 2;
            }
        }

        $partitions[$predicateCount][] = $element;
    }

    return $partitions;
}

/**
 * Divide collection by given classes
 *
 * REPL:
 * >>> partitionOf(
 *    [new Foo(1), new Bar(2)],
 *    Foo::class,
 *    Bar::class
 * );
 * => array{list<Foo>, list<Bar>, list<Foo|Bar>}
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string ...$classes
 *
 * @see PartitionOfFunctionReturnTypeProvider
 */
function partitionOf(iterable $collection, bool $invariant, string ...$classes): array
{
    $partitions = array_fill(0, count($classes) + 1, []);

    foreach ($collection as $element) {
        foreach ($classes as $partition => $c) {
            if (of($element, $c, $invariant)) {
                $partitions[$partition][] = $element;
                continue 2;
            }
        }

        $partitions[func_num_args() - 2][] = $element;
    }

    return $partitions;
}

