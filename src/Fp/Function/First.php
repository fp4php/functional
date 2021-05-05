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
function first(iterable $collection, ?callable $callback = null): Option
{
    if (is_null($callback)) {
        return head($collection);
    }

    $first = null;

    foreach ($collection as $index => $element) {
        if (call_user_func($callback, $element, $index)) {
            $first = $element;
            break;
        }
    }

    return Option::of($first);
}
