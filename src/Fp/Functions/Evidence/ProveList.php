<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

use function Fp\Collection\everyOf;
use function Fp\Collection\head;
use function Fp\Collection\keys;

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<list<TV>>
 */
function proveList(iterable $collection): Option
{
    return Option::do(function () use ($collection) {
        $array = yield proveArray($collection);
        yield proveTrue(isSequence(keys($array)));

        /** @var list<TV> $array */
        return $array;
    });
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<non-empty-list<TV>>
 */
function proveNonEmptyList(iterable $collection): Option
{
    return Option::do(function () use ($collection) {
        $list = yield proveList($collection);
        yield head($list);

        /** @var non-empty-list<TV> $list */
        return $list;
    });
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn
 *
 * @psalm-return Option<list<TVO>>
 */
function proveListOf(iterable $collection, string $fqcn, bool $strict = false): Option
{
    return Option::do(function () use ($collection, $fqcn, $strict) {
        $list = yield proveList($collection);
        yield proveTrue(everyOf($list, $fqcn, $strict));

        /** @var list<TVO> $list */
        return $list;
    });
}
