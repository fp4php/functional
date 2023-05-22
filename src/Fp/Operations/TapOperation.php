<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

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
        foreach ($this->gen as $key => $value) {
            $f($key, $value);
            yield $key => $value;
        }
    }
}
