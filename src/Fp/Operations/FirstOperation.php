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
     * @param null|callable(TK, TV): bool $f
     * @return Option<TV>
     */
    public function __invoke(?callable $f = null): Option
    {
        if (is_null($f)) {
            $f = fn(mixed $_key, mixed $value): bool => true;
        }

        $first = null;

        foreach ($this->gen as $key => $value) {
            if ($f($key, $value)) {
                $first = $value;
                break;
            }
        }

        return Option::fromNullable($first);
    }
}
