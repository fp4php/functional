<?php

declare(strict_types=1);

namespace Fp\Collections;

use Iterator;

/**
 * @template A
 * @template TInit
 */
final class Folding
{
    /**
     * @param Iterator<A> $iterator
     * @param TInit $acc
     */
    public function __construct(
        public Iterator $iterator,
        public mixed $acc,
    ) {}

    /**
     * @template TFold
     *
     * @param callable(TInit, A): TFold $callback
     * @return TFold|TInit
     */
    public function __invoke(callable $callback): mixed
    {
        $acc = $this->acc;

        foreach ($this->iterator as $value) {
            /** @psalm-suppress PossiblyInvalidArgument */
            $acc = $callback($acc, $value);
        }

        return $acc;
    }
}
