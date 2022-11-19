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
final class IntersperseOperation extends AbstractOperation
{
    /**
     * @template TVI
     *
     * @param TVI $separator
     * @return Generator<TK|int, TV|TVI>
     */
    public function __invoke(mixed $separator): Generator
    {
        return asGenerator(function () use ($separator) {
            $isFirst = true;

            foreach ($this->gen as $elem) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    yield $separator;
                }

                yield $elem;
            }
        });
    }
}
