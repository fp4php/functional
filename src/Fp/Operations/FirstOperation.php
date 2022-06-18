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
class FirstOperation extends AbstractOperation
{
    /**
     * @param null|callable(TV, TK): bool $f
     * @return Option<TV>
     */
    public function __invoke(?callable $f = null): Option
    {
        if (is_null($f)) {
            $f = fn(mixed $value, mixed $key): bool => true;
        }

        $first = null;

        foreach ($this->gen as $key => $value) {
            if ($f($value, $key)) {
                $first = $value;
                break;
            }
        }

        return Option::fromNullable($first);
    }
}
