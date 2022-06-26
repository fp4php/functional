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
final class DropWhileOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TV): bool $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            $toggle = true;

            foreach ($this->gen as $key => $value) {
                if (!($toggle = $toggle && $f($value))) {
                    yield $key => $value;
                }
            }
        });
    }
}
