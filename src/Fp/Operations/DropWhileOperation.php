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
final class DropWhileOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TK, TV): bool $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        $toggle = true;

        foreach ($this->gen as $key => $value) {
            if (!($toggle = $toggle && $f($key, $value))) {
                yield $key => $value;
            }
        }
    }
}
