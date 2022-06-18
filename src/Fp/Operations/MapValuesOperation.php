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
class MapValuesOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV, TK): TVO $f
     * @return Generator<TK, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                yield $key => $f($value, $key);
            }
        });
    }
}
