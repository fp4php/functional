<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

use function Fp\Collection\everyOf;
use function Fp\Collection\head;

/**
 * Prove that given collection is of array type
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<array<TK, TV>>
 */
function proveArray(iterable $collection): Option
{
    return Option::of(is_array($collection) ? $collection : null);
}

/**
 * Prove that given collection is of non-empty-array type
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 *
 * @psalm-return Option<non-empty-array<TK, TV>>
 */
function proveNonEmptyArray(iterable $collection): Option
{
    return Option::do(function () use ($collection) {
        $array = yield proveArray($collection);
        yield head($array);

        /** @var non-empty-array<TK, TV> $array */
        return $array;
    });
}

/**
 * Prove that collection is of array type
 * and every element is of given class
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-return Option<array<TK, TVO>>
 */
function proveArrayOf(iterable $collection, string $fqcn, bool $invariant = false): Option
{
    return Option::do(function () use ($collection, $fqcn, $invariant) {
        $array = yield proveArray($collection);
        yield proveTrue(everyOf($array, $fqcn, $invariant));

        /** @var array<TK, TVO> $array */
        return $array;
    });
}

/**
 * Prove that collection is of non-empty-array type
 * and every element is of given class
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TVO> $fqcn fully qualified class name
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-return Option<non-empty-array<TK, TVO>>
 */
function proveNonEmptyArrayOf(iterable $collection, string $fqcn, bool $invariant = false): Option
{
    return Option::do(function () use ($collection, $fqcn, $invariant) {
        $array = yield proveArrayOf($collection, $fqcn, $invariant);
        yield head($array);

        /** @var non-empty-array<TK, TVO> $array */
        return $array;
    });
}

