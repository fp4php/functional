<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\Map;
use Fp\Collections\NonEmptyMap;
use Generator;

/**
 * @template TK
 * @template TV
 * @extends AbstractOperation<TK, TV>
 */
final class MergeMapOperation extends AbstractOperation
{
    /**
     * @template TKO
     * @template TVO
     *
     * @param Map<TKO, TVO>|NonEmptyMap<TKO, TVO> $map
     * @return Generator<TK|TKO, TV|TVO>
     */
    public function __invoke(Map|NonEmptyMap $map): Generator
    {
        foreach ($this->gen as $key => $value) {
            yield $key => $value;
        }
        foreach ($map as [$key, $value]) {
            yield $key => $value;
        }
    }
}
