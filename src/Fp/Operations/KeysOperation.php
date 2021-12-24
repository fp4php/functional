<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class KeysOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @return Generator<int, TK>
     */
    public function __invoke(): Generator
    {
        return asGenerator(function () {
            foreach ($this->gen as $key => $value) {
                yield $key;
            }
        });
    }
}
