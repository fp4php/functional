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
class PrependedAllOperation extends AbstractOperation
{
    /**
     * @template TVI
     *
     * @param iterable<mixed, TVI> $prefix
     * @return Generator<TV|TVI>
     */
    public function __invoke(iterable $prefix): Generator
    {
        return asGenerator(function () use ($prefix) {
            foreach ($prefix as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($this->gen as $suffixElem) {
                yield $suffixElem;
            }
        });
    }
}
