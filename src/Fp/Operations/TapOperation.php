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
final class TapOperation extends AbstractOperation
{
    /**
     * @param callable(TK, TV): void $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $f($key, $value);
                yield $key => $value;
            }
        });
    }
}
