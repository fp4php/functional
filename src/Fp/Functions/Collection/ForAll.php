<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\TapOperation;
use Fp\Streams\Stream;

/**
 * Do something for all collection elements
 *
 * ```php
 * >>> forAll([1, 2, 3], function($v) { echo($v); });
 * => 123
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): void $callback
 */
function forAll(iterable $collection, callable $callback): void
{
    Stream::emits(TapOperation::of($collection)($callback))->drain();
}
