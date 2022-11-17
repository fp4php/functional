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
final class MaxByElementOperation extends AbstractOperation
{
    /**
     * @param callable(TV): mixed $by
     * @return Option<TV>
     */
    public function __invoke(callable $by): Option
    {
        $max = null;

        foreach ($this->gen as $item) {
            if (null === $max) {
                $max = $item;
                continue;
            }

            if ($by($max) < $by($item)) {
                $max = $item;
            }
        }

        return Option::fromNullable($max);
    }
}
