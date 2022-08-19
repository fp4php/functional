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
final class ReindexOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TK, TV): TKO $f
     * @return Generator<TKO, TV>
     */
    public function __invoke(callable $f): Generator
    {
        foreach ($this->gen as $key => $value) {
            yield $f($key, $value) => $value;
        }
    }
}
