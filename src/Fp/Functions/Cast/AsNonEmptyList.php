<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

use function Fp\Collection\head;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return Option<non-empty-list<TV>>
 */
function asNonEmptyList(iterable $collection): Option
{
    /** @var Option<non-empty-list<TV>> $list */
    $list = head($collection)->map(fn() => asList($collection));

    return $list;
}
