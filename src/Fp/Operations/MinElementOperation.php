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
final class MinElementOperation extends AbstractOperation
{
    /**
     * @return Option<TV>
     */
    public function __invoke(): Option
    {
        $min = null;

        foreach ($this->gen as $val) {
            if (null === $min || $min > $val) {
                $min = $val;
            }
        }

        return Option::fromNullable($min);
    }
}
