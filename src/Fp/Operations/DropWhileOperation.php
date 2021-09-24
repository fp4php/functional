<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Callable\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class DropWhileOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @template TKO
     * @psalm-param callable(TV, TK): bool $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            $toggle = true;

            foreach ($this->gen as $key => $value) {
                if (!($toggle = $toggle && $f($value, $key))) {
                    yield $key => $value;
                }
            }
        });
    }
}
