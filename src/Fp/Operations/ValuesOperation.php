<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
class ValuesOperation extends AbstractOperation
{
    /**
     * @return Generator<int, TV>
     */
    public function __invoke(): Generator
    {
        return asGenerator(function () {
            foreach ($this->gen as $value) {
                yield $value;
            }
        });
    }
}
