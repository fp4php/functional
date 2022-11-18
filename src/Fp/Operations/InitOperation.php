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
final class InitOperation extends AbstractOperation
{
    /**
     * @return Generator<TK, TV>
     */
    public function __invoke(): Generator
    {
        if (!$this->gen->valid()) {
            return [];
        }

        while (true) {
            $item = $this->gen->current();
            $key = $this->gen->key();

            $this->gen->next();

            if (!$this->gen->valid()) {
                break;
            }

            yield $key => $item;
        }
    }
}
