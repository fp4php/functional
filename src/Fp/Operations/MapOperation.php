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
final class MapOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param callable(TV): TVO $f
     * @return Generator<TK, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        $withKey =
            /**
             * @param TV $value
             * @return TVO
             */
            fn(mixed $_, mixed $value) => $f($value);

        return MapWithKeyOperation::of($this->gen)($withKey);
    }
}
