<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Either\Either;
use Fp\Functional\Separated\Separated;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class PartitionMapOperation extends AbstractOperation
{
    /**
     * @template L
     * @template R
     *
     * @param callable(TK, TV): Either<L, R> $callback
     * @return Separated<array<TK, L>, array<TK, R>>
     */
    public function __invoke(callable $callback): Separated
    {
        $left = [];
        $right = [];

        foreach ($this->gen as $k => $v) {
            $result = $callback($k, $v);

            if ($result->isLeft()) {
                $left[$k] = $result->get();
            } else {
                $right[$k] = $result->get();
            }
        }

        return Separated::create($left, $right);
    }
}
