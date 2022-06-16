<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends AbstractOperation<TK, TV>
 */
class CountOperation extends AbstractOperation
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
