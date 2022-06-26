<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class CountOperation extends AbstractOperation
{
    public function __invoke(): int
    {
        $counter = 0;

        foreach ($this->gen as $ignored) {
            $counter++;
        }

        return $counter;
    }
}
