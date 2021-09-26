<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\ChunksOperation;
use Generator;

/**
 * Generate collection chunks
 *
 * Warning: you can not rewind the generator
 *
 * ```php
 * >>> chunks([1, 2, 3, 4], 2);
 * => [[1, 2], [3, 4]]
 * ```
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param positive-int $chunkSize
 * @psalm-return Generator<non-empty-list<TV>>
 */
function chunks(iterable $collection, int $chunkSize): Generator
{
    return ChunksOperation::of($collection)($chunkSize);
}
