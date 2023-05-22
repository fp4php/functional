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
final class PrependedOperation extends AbstractOperation
{
    /**
     * @template TVI
     *
     * @param TVI $elem
     * @return Generator<TV|TVI>
     */
    public function __invoke(mixed $elem): Generator
    {
        yield $elem;

        foreach ($this->gen as $suffixElem) {
            yield $suffixElem;
        }
    }
}
