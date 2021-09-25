<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class LastOperation extends AbstractOperation
{
    /**
     * @param null|callable(TV, TK): bool $f
     * @return Option<TV>
     */
    public function __invoke(?callable $f = null): Option
    {
        $last = null;

        foreach ($this->gen as $key => $value) {
            if (is_null($f) || $f($value, $key)) {
                $last = $value;
            }
        }

        return Option::fromNullable($last);
    }
}
