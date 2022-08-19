<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class ButLastOperation extends AbstractOperation
{
    /**
     * @return array<TK, TV>
     */
    public function __invoke(): array
    {
        $butLast = iterator_to_array($this->gen);
        array_pop($butLast);

        return $butLast;
    }
}
