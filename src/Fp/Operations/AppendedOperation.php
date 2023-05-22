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
final class AppendedOperation extends AbstractOperation
{
    /**
     * @template TVI
     *
     * @param TVI $elem
     * @return Generator<TV|TVI>
     */
    public function __invoke(mixed $elem): Generator
    {
        foreach ($this->gen as $prefixElem) {
            yield $prefixElem;
        }

        yield $elem;
    }
}
