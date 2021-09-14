<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;

/**
 * Returns second collection element
 * None if there is no second collection element
 *
 * REPL:
 * >>> second([1, 2, 3])->get();
 * => 2
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return Option<TV>
 */
function second(iterable $collection): Option
{
    $i = 0;
    $second = null;

    foreach ($collection as $elem) {
        if (1 === $i) {
            $second = $elem;
            break;
        }
        $i++;
    }

    return Option::fromNullable($second);
}
