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
final class FilterNotNullOperation extends AbstractOperation
{
    /**
     * @return Generator<TK, TV>
     */
    public function __invoke(): Generator
    {
        foreach ($this->gen as $key => $value) {
            if (null !== $value) {
                yield $key => $value;
            }
        }
    }
}
