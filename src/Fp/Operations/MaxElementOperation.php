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
final class MaxElementOperation extends AbstractOperation
{
    /**
     * @return Option<TV>
     */
    public function __invoke(): Option
    {
        $max = null;

        foreach ($this->gen as $val) {
            if (null === $max || $max < $val) {
                $max = $val;
            }
        }

        return Option::fromNullable($max);
    }
}
