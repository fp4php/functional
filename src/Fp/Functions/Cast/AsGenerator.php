<?php

declare(strict_types=1);

namespace Fp\Cast;

use Generator;

/**
 * ```php
 * >>> asGenerator(function() { yield 1; yield 2; });
 * => Generator(1, 2)
 * ```
 *
 * @template TK
 * @template TV
 *
 * @param callable(): (iterable<TK, TV> | Generator<TK, TV>) $callback
 * @return Generator<TK, TV>
 */
function asGenerator(callable $callback): Generator
{
    $iter = $callback();

    if ($iter instanceof Generator) {
        return $iter;
    }

    $generator = function() use ($iter): Generator {
        foreach ($iter as $key => $value) {
            yield $key => $value;
        }
    };

    return $generator();
}
