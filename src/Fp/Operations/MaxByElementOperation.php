<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

/**
 * @psalm-immutable
 * @template TK
 * @template TV
 * @extends AbstractOperation<TK, TV>
 */
class MaxByElementOperation extends AbstractOperation
{
    /**
     * @psalm-param callable(TV): mixed $by
     * @return Option<TV>
     */
    public function __invoke(callable $by): Option
    {
        $f =
            /**
             * @param TV $r
             * @param TV $l
             * @return int
             */
            fn(mixed $r, mixed $l): int => $by($l) <=> $by($r);

        $gen = SortedOperation::of($this->gen)($f);

        return FirstOperation::of($gen)();
    }
}