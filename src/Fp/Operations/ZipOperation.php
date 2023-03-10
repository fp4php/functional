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
final class ZipOperation extends AbstractOperation
{
    /**
     * @template TVI
     *
     * @param iterable<mixed, TVI> $that
     * @return Generator<int, array{TV, TVI}>
     */
    public function __invoke(iterable $that): Generator
    {
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
    }
}
