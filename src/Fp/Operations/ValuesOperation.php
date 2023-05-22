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
final class ValuesOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TV>
     */
    public function __invoke(): Generator
    {
        foreach ($this->gen as $value) {
            yield $value;
        }
    }
}
