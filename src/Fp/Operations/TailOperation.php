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
final class TailOperation extends AbstractOperation
{
    /**
     * @return Generator<TK, TV>
     */
    public function __invoke(): Generator
    {
        $isFirst = true;

        foreach ($this->gen as $key => $value) {
            if ($isFirst) {
                $isFirst = false;
                continue;
            }

            yield $key => $value;
        }
    }
}
