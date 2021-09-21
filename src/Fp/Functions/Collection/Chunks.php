<?php

declare(strict_types=1);

namespace Fp\Collection;

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
 * @psalm-return Generator<list<TV>>
 */
function chunks(iterable $collection, int $chunkSize): Generator
{
    $chunk = [];
    $i = 0;

    foreach ($collection as $element) {
        $i++;
        $chunk[] = $element;

        if (0 === $i % $chunkSize) {
            yield $chunk;
            $chunk = [];
        }
    }

    if (!empty($chunk)) {
        yield $chunk;
    }
}
