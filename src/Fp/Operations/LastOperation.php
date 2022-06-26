<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
class LastOperation extends AbstractOperation
{
    /**
     * @param null|callable(TV): bool $f
     * @return Option<TV>
     */
    public function __invoke(?callable $f = null): Option
    {
        $last = null;

        foreach ($this->gen as $value) {
            if (is_null($f) || $f($value)) {
                $last = $value;
            }
        }

        return Option::fromNullable($last);
    }
}
