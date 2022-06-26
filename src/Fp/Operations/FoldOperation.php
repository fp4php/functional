<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class FoldOperation extends AbstractOperation
{
    /**
     * @template TA
     *
     * @param TA $init
     * @param callable(TA, TV): TA $f
     * @return TA
     */
    public function __invoke(mixed $init, callable $f): mixed
    {
        $acc = $init;

        foreach ($this->gen as $value) {
            $acc = $f($acc, $value);
        }

        return $acc;
    }
}
