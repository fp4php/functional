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
class TapOperation extends AbstractOperation
{
    /**
     * @param callable(TV, TK): void $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                $f($value, $key);
                yield $key => $value;
            }
        });
    }
}
