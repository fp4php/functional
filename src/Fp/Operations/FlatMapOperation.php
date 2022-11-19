<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;
use Fp\Collections\Collection;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class FlatMapOperation extends AbstractOperation
{
    /**
     * @template TKO
     * @template TVO
     *
     * @param callable(TK, TV): (iterable<TKO, TVO>|Collection<TKO, TVO>) $f
     * @return Generator<TKO, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $xs = $f($key, $value);

                foreach ($xs as $k => $x) {
                    yield $k => $x;
                }
            }
        });
    }
}
