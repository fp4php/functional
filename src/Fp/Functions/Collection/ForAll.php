<?php

declare(strict_types=1);

namespace Fp\Collection;

/**
 * Do something for all collection elements
 *
 * REPL:
 * >>> forAll([1, 2, 3], function($v) { echo($v); });
 * => 123
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): void $callback
 */
function forAll(iterable $collection, callable $callback): void
{
    foreach ($collection as $index => $element) {
        $callback($element, $index);
    }
}
