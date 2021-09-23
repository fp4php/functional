<?php

declare(strict_types=1);

namespace Fp\Collections\Operations;

use Generator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class AppendedOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TVI
     * @psalm-param TVI $elem
     * @return Generator<TV|TVI>
     */
    public function __invoke(mixed $elem): Generator
    {
        return asGenerator(function () use ($elem) {
            foreach ($this->gen as $prefixElem) {
                yield $prefixElem;
            }

            yield $elem;
        });
    }
}
