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
class ZipOperation extends AbstractOperation
{
    /**
     * @template TVI
     * @param iterable<TVI> $that
     * @return Generator<int, array{TV, TVI}>
     */
    public function __invoke(iterable $that): Generator
    {
        return asGenerator(function () use ($that) {
            $thisIter = $this->gen;
            $thatIter = asGenerator(fn() => $that);

            $thisIter->rewind();
            $thatIter->rewind();

            while ($thisIter->valid() && $thatIter->valid()) {
                $thisElem = $thisIter->current();
                $thatElem = $thatIter->current();

                yield [$thisElem, $thatElem];

                $thisIter->next();
                $thatIter->next();
            }
        });
    }
}
