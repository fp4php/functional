<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\ArrayList;
use Generator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class RepeatNOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TV>
     */
    public function __invoke(int $times): Generator
    {
        return asGenerator(function () use ($times) {
            $buffer = ArrayList::collect($this->gen);

            foreach ($buffer as $elem) {
                yield $elem;
            }

            for($i = 0; $i < $times - 1; $i++) {
                foreach ($buffer as $elem) {
                    yield $elem;
                }
            }
        });
    }
}
