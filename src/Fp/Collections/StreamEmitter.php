<?php

declare(strict_types=1);

namespace Fp\Collections;

/**
 * @psalm-immutable
 * @template-covariant TV
 */
interface StreamEmitter
{
    /**
     * REPL:
     * >>> Stream::emits([1, 2])
     * => Stream(1, 2)
     *
     * @template TVI
     * @param iterable<TVI> $source
     * @return self<TVI>
     */
    public static function emits(iterable $source): self;
}
