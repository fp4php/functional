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
class SecondOperation extends AbstractOperation
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

        $i = 0;
        $second = null;

        foreach ($this->gen as $value) {
            if ($f($value) && 1 === $i) {
                $second = $value;
                break;
            }

            if ($f($value)) {
                $i++;
            }
        }

        return Option::fromNullable($second);
    }
}
