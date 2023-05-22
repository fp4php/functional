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
final class PrependedAllOperation extends AbstractOperation
{
    /**
     * @template TVI
     *
     * @param iterable<mixed, TVI> $prefix
     * @return Generator<TV|TVI>
     */
    public function __invoke(iterable $prefix): Generator
    {
        foreach ($prefix as $prefixElem) {
            yield $prefixElem;
        }

        foreach ($this->gen as $suffixElem) {
            yield $suffixElem;
        }
    }
}
