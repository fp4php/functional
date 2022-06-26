<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
class ExistsOperation extends AbstractOperation
{
    /**
     * @param callable(TV): bool $f
     */
    public function __invoke(callable $f): bool
    {
        $exists = false;

        foreach ($this->gen as $value) {
            if ($f($value)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }
}
