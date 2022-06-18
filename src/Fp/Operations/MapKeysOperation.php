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
class MapKeysOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TV, TK): TKO $f
     * @return Generator<TKO, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                yield $f($value, $key) => $value;
            }
        });
    }
}
