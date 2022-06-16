<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-suppress InvalidTemplateParam
 * @extends AbstractOperation<TK, TV>
 */
class FoldOperation extends AbstractOperation
{
    /**
     * @template TA
     * @psalm-param TA $init
     * @psalm-param callable(TA, TV, TK): TA $f
     * @psalm-return TA
     */
    public function __invoke(mixed $init, callable $f): mixed
    {
        $acc = $init;

        foreach ($this->gen as $key => $value) {
            $acc = $f($acc, $value, $key);
        }

        return $acc;
    }
}
