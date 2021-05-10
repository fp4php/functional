<?php

declare(strict_types=1);

namespace Fp\Cast;

use Fp\Functional\Option\Option;

use function Fp\Collection\head;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-return Option<non-empty-array<TK, TV>>
 */
function asNonEmptyArray(iterable $collection): Option
{
    /** @var Option<non-empty-array<TK, TV>> $array */
    $array = head($collection)->map(fn() => asArray($collection));

    return $array;
}
