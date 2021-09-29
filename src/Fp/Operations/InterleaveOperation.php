<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class InterleaveOperation extends AbstractOperation
{
    /**
     * @template TVI
     * @param iterable<TVI> $that
     * @return Generator<int, TV|TVI>
     */
    public function __invoke(iterable $that): Generator
    {
        $pairs = ZipOperation::of($this->gen)($that);

        return FlatMapOperation::of($pairs)(function (array $pair) {
            yield from $pair;
        });
    }
}
