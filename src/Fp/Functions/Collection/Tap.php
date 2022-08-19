<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\TapOperation;
use Fp\Streams\Stream;
use function Fp\Callable\dropFirstArg;

/**
 * Do something for all collection elements
 *
 * ```php
 * >>> tap([1, 2, 3], function($v) { echo($v); });
 * => 123
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @param callable(TV): void $callback
 */
function tap(iterable $collection, callable $callback): void
{
    Stream::emits(TapOperation::of($collection)(dropFirstArg($callback)))->drain();
}
