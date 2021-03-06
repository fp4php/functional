<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\MapKeysOperation;

use function Fp\Cast\asArray;

/**
 * Produces a new array of elements by assigning the values to keys generated by a transformation function (callback).
 *
 * ```php
 * >>> reindex([1, 'a' => 2], fn (int $value) => $value);
 * => [1 => 1, 2 => 2]
 * ```
 *
 * @psalm-template TKI of array-key
 * @psalm-template TKO of array-key
 * @psalm-template TV
 * @psalm-param iterable<TKI, TV> $collection
 * @psalm-param callable(TV, TKI): TKO $callback
 * @psalm-return array<TKO, TV>
 */
function reindex(iterable $collection, callable $callback): array
{
    return asArray(MapKeysOperation::of($collection)($callback));
}
