<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\ArrayList;
use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class RepeatOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TV>
     */
    public function __invoke(): Generator
    {
        return asGenerator(function () {
            $buffer = ArrayList::collect($this->gen);

            foreach ($buffer as $elem) {
                yield $elem;
            }

            while(true) {
                foreach ($buffer as $elem) {
                    yield $elem;
                }
            }
        });
    }
}
