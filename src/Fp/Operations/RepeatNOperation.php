<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\ArrayList;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class RepeatNOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TV>
     */
    public function __invoke(int $times): Generator
    {
        $buffer = ArrayList::collect($this->gen);

        foreach ($buffer as $elem) {
            yield $elem;
        }

        for ($i = 0; $i < $times - 1; $i++) {
            foreach ($buffer as $elem) {
                yield $elem;
            }
        }
    }
}
