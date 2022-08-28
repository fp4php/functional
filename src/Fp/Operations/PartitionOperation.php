<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Separated\Separated;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class PartitionOperation extends AbstractOperation
{
    /**
     * @param callable(TK, TV): bool $predicate
     * @return Separated<array<TK, TV>, array<TK, TV>>
     */
    public function __invoke(callable $predicate): Separated
    {
        $left = [];
        $right = [];

        foreach ($this->gen as $k => $v) {
            if (!$predicate($k, $v)) {
                $left[$k] = $v;
            } else {
                $right[$k] = $v;
            }
        }

        return Separated::create($left, $right);
    }
}
