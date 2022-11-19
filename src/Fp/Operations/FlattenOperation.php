<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

final class FlattenOperation
{
    /**
     * @template TKO
     * @template TVO
     *
     * @param iterable<iterable<TKO, TVO>> $collection
     * @return Generator<TKO, TVO>
     */
    public static function of(iterable $collection): Generator
    {
        foreach ($collection as $innerCollection) {
            foreach ($innerCollection as $key => $value) {
                yield $key => $value;
            }
        }
    }
}
