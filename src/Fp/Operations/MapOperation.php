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
     * @param callable(TK, TV): TVO $f
     * @return Generator<TK, TVO>
     */
    public function __invoke(callable $f): Generator
    {
        return asGenerator(function () use ($f) {
            foreach ($this->gen as $key => $value) {
                yield $key => $f($key, $value);
            }
        });
    }

    /**
     * @template TKI
     * @template TVI
     * @template TVO
     *
     * @param iterable<TKI, TVI> $input
     * @param callable(TVI): TVO $f
     * @return Generator<TKI, TVO>
     */
    public static function withoutKey(iterable $input, callable $f): Generator
    {
        $withKey =
            /**
             * @param TKI $_key
             * @param TVI $value
             * @return TVO
             */
            fn(mixed $_key, mixed $value) => $f($value);

        return MapOperation::of($input)($withKey);
    }
}
