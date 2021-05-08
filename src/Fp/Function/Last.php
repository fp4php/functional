<?php

declare(strict_types=1);

namespace Fp\Function;

use Fp\Functional\Option\Option;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param null|callable(TV, TK): bool $callback
 *
 * @psalm-return Option<TV>
 */
function last(iterable $collection, ?callable $callback = null): Option
{
    $last = null;

    foreach ($collection as $index => $element) {
        if (is_null($callback) || call_user_func($callback, $element, $index)) {
            $last = $element;
        }
    }

    return Option::of($last);
}
