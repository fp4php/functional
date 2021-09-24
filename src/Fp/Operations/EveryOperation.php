<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class EveryOperation extends AbstractOperation
{
    /**
     * @template TVO
     * @param callable(TV, TK): bool $f
     * @return bool
     */
    public function __invoke(callable $f): bool
    {
        $res = true;

        foreach ($this->gen as $key => $value) {
            if (!$f($value, $key)) {
                $res = false;
                break;
            }
        }

        return $res;
    }
}
