<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends AbstractOperation<TK, TV>
 */
class AppendedAllOperation extends AbstractOperation
{
    /**
     * @template TVI
     * @psalm-param iterable<TVI> $suffix
     * @return Generator<TV|TVI>
     */
    public function __invoke(iterable $suffix): Generator
    {
        return asGenerator(function () use ($suffix) {
            foreach ($this->gen as $prefixElem) {
                yield $prefixElem;
            }

            foreach ($suffix as $suffixElem) {
                yield $suffixElem;
            }
        });
    }
}
