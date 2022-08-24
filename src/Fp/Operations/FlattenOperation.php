<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

final class FlattenOperation
{
    /**
     * @template TVO
     *
     * @param iterable<iterable<TVO>> $collection
     * @return Generator<int, TVO>
     */
    public static function of(iterable $collection): Generator
    {
        foreach ($collection as $innerCollection) {
            foreach ($innerCollection as $value) {
                yield $value;
            }
        }
    }
}
