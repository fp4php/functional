<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class FilterOperation extends AbstractOperation
{
    /**
     * @param callable(TV): bool $f
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $f): Generator
    {
        $withKey =
            /** @param TV $value */
            fn(mixed $_, mixed $value): bool => $f($value);

        return FilterWithKeyOperation::of($this->gen)($withKey);
    }
}
