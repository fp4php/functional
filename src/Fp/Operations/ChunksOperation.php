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
final class ChunksOperation extends AbstractOperation
{
    /**
     * @param positive-int $size
     * @return Generator<int, non-empty-list<TV>>
     */
    public function __invoke(int $size): Generator
    {
        $chunk = [];
        $i = 0;

        foreach ($this->gen as $value) {
            $i++;

            $chunk[] = $value;

            if (0 === $i % $size) {
                yield $chunk;
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            yield $chunk;
        }
    }
}
