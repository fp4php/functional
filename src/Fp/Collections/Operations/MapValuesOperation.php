<?php

declare(strict_types=1);

namespace Fp\Collections\Operations;

use Generator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class MapValuesOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TVO
     * @param callable(TV, TK): TVO $f
     * @return Generator<TK, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->input as $key => $value) {
                yield $key => $f($value, $key);
            }
        });
    }
}
