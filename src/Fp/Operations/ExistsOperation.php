<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class ExistsOperation extends AbstractOperation
{
    /**
     * @param callable(TK, TV): bool $f
     */
    public function __invoke(callable $f): bool
    {
        $exists = false;

        foreach ($this->gen as $key => $value) {
            if ($f($key, $value)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }
}
