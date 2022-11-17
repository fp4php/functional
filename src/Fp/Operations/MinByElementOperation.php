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
final class MinByElementOperation extends AbstractOperation
{
    /**
     * @param callable(TV): mixed $by
     * @return Option<TV>
     */
    public function __invoke(callable $by): Option
    {
        $min = null;

        foreach ($this->gen as $item) {
            if (null === $min) {
                $min = $item;
                continue;
            }

            if ($by($min) > $by($item)) {
                $min = $item;
            }
        }

        return Option::fromNullable($min);
    }
}
