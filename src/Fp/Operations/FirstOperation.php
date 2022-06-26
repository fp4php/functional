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
final class FirstOperation extends AbstractOperation
{
    /**
     * @param null|callable(TV): bool $f
     * @return Option<TV>
     */
    public function __invoke(?callable $f = null): Option
    {
        if (is_null($f)) {
            $f = fn(mixed $value): bool => true;
        }

        $first = null;

        foreach ($this->gen as $value) {
            if ($f($value)) {
                $first = $value;
                break;
            }
        }

        return Option::fromNullable($first);
    }
}
