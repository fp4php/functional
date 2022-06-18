<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
class FlatMapOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV, TK): iterable<mixed, TVO> $f
     * @return Generator<int, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $xs = $f($value, $key);

                foreach ($xs as $x) {
                    yield $x;
                }
            }
        });
    }
}
