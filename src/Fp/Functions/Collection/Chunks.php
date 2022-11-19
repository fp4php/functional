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
 * @template TV
 *
 * @param iterable<TV> $collection
 * @param positive-int $chunkSize
 * @return Generator<non-empty-list<TV>>
 */
function chunks(iterable $collection, int $chunkSize): Generator
{
    return ChunksOperation::of($collection)($chunkSize);
}
