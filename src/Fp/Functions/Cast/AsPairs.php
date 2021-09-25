<?php

declare(strict_types=1);

namespace Fp\Callable;

use function Fp\Cast\asList;

/**
 * ```php
 * >>> asPairs(['a' => 1, 'b' => 2]);
 * => [['a', 1], ['b', 2]]
 * ```
 *
 * @psalm-template TK
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return list<array{TK, TV}>
 */
function asPairs(iterable $collection): array
{
    return asList(asGenerator(function () use ($collection) {
        foreach ($collection as $key => $value) {
            yield [$key, $value];
        }
    }));
}

