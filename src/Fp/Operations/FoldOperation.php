<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template A
 * @template TInit
 */
final class FoldOperation
{
    /**
     * @param iterable<A> $iterator
     * @param TInit $init
     */
    public function __construct(
        public iterable $iterator,
        public mixed $init,
    ) {}

    /**
     * @template TFold
     *
     * @param callable(TInit, A): TFold $callback
     * @return TFold|TInit
     */
    public function __invoke(callable $callback): mixed
    {
        $acc = $this->init;

        foreach ($this->iterator as $value) {
            /** @psalm-suppress PossiblyInvalidArgument */
            $acc = $callback($acc, $value);
        }

        return $acc;
    }
}
