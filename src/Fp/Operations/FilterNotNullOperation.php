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
class FilterNotNullOperation extends AbstractOperation
{
    /**
     * @return Generator<TK, TV>
     */
    public function __invoke(): Generator
    {
        return asGenerator(function () {
            foreach ($this->gen as $key => $value) {
                if (null !== $value) {
                    yield $key => $value;
                }
            }
        });
    }
}
