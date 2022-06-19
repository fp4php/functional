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
final class ReindexOperation extends AbstractOperation
{
    /**
     * @template TKO
     *
     * @param callable(TV): TKO $f
     * @return Generator<TKO, TV>
     */
    public function __invoke(callable $f): Generator
    {
        $withKey =
            /**
             * @param TV $value
             * @return TKO
             */
            fn(mixed $_, mixed $value) => $f($value);

        return ReindexWithKeyOperation::of($this->gen)($withKey);
    }
}
