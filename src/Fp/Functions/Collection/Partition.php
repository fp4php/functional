<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\Hook\FunctionReturnTypeProvider\PartitionNFunctionReturnTypeProvider;

/**
 * Divide collection by given conditions
 *
 *
 * ```php
 * >>> partitionN([new Foo(1), new Foo(2), new Bar(3)],
 * >>>     fn(Foo|Bar $v) => objectOf($v, Foo::class),
 * >>>     fn(Foo|Bar $v) => objectOf($v, Bar::class),
 * >>> )
 * => [[Foo(1), new Foo(1)], [new Bar(3)], []]
 * ```
 *
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param callable(TV): bool ...$predicates
 *
 * @no-named-arguments
 * @see PartitionNFunctionReturnTypeProvider
 */
function partitionN(iterable $collection, callable ...$predicates): array
{
    $predicateCount = count($predicates);
    $partitions = array_fill(0, $predicateCount + 1, []);

    foreach ($collection as $element) {
        foreach ($predicates as $partition => $callback) {
            if ($callback($element)) {
                $partitions[$partition][] = $element;
                continue 2;
            }
        }

        $partitions[$predicateCount][] = $element;
    }

    return $partitions;
}
