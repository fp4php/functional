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
class MapKeysOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TKO
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
