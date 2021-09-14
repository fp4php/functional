<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Generator;

use function Fp\of;

/**
 * @psalm-immutable
 * @template-covariant TV
 * @psalm-require-implements StreamEmitter
 */
trait MapOp
{
    /**
     * @template TVO
     * @psalm-param callable(TV): TVO $callback
     * @psalm-return self<TVO>
     */
    public function map(callable $callback): self
    {
        return self::emits(IterableOnce::of(function () use ($callback) {
            foreach ($this as $elem) {
                /** @var TV $e */
                $e = $elem;

                yield $callback($e);
            }
        }));
    }
}

