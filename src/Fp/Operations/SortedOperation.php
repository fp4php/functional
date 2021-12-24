<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\ArrayList;
use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class SortedOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-param callable(TV, TV): int $f
     * @return Generator<TV>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            $sorted = ArrayList::collect($this->gen)->toArray();
            usort($sorted, $f);

            foreach ($sorted as $value) {
                yield $value;
            }
        });
    }
}
