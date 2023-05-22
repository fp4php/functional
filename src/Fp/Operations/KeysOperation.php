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
final class KeysOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TK>
     */
    public function __invoke(): Generator
    {
        foreach ($this->gen as $key => $value) {
            yield $key;
        }
    }
}
