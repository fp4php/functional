<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

use function Fp\Collection\everyOf;
use function Fp\Collection\head;

/**
 * Prove that given collection is of array type
 *
 * ```php
 * >>> proveArray([1, 2]);
 * => Some([1, 2])
 *
 * >>> proveArray(true);
 * => None
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return Option<array<TK, TV>>
 */
function proveArray(iterable $collection): Option
{
    return Option::fromNullable(is_array($collection) ? $collection : null);
}

/**
 * Prove that given collection is of non-empty-array type
 *
 * ```php
 * >>> proveNonEmptyArray([1, 2]);
 * => Some([1, 2])
 *
 * >>> proveNonEmptyArray([]);
 * => None
 * ```
 *
 * @template TK of array-key
 * @template TV
 *
 * @param iterable<TK, TV> $collection
 * @return Option<non-empty-array<TK, TV>>
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
 * ```php
 * >>> proveArrayOf([new Foo(1), new Foo(2)]);
 * => Some([Foo(1), Foo(2)])
 *
 * >>> proveArrayOf([new Foo(1), 2]);
 * => None
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn fully qualified class name
 * @param bool $invariant if turned on then subclasses are not allowed
 * @return Option<array<TK, TVO>>
 */
function proveArrayOf(iterable $collection, string|array $fqcn, bool $invariant = false): Option
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
 * ```php
 * >>> proveNonEmptyArrayOf([new Foo(1), new Foo(2)]);
 * => Some([Foo(1), Foo(2)])
 *
 * >>> proveNonEmptyArrayOf([new Foo(1), 2]);
 * => None
 *
 * >>> proveNonEmptyArrayOf([]);
 * => None
 * ```
 *
 * @template TK of array-key
 * @template TV
 * @template TVO
 *
 * @param iterable<TK, TV> $collection
 * @param class-string<TVO>|list<class-string<TVO>> $fqcn fully qualified class name
 * @param bool $invariant if turned on then subclasses are not allowed
 * @return Option<non-empty-array<TK, TVO>>
 */
function proveNonEmptyArrayOf(iterable $collection, string|array $fqcn, bool $invariant = false): Option
{
    return Option::do(function () use ($collection, $fqcn, $invariant) {
        $array = yield proveArrayOf($collection, $fqcn, $invariant);
        yield head($array);

        /** @var non-empty-array<TK, TVO> $array */
        return $array;
    });
}

