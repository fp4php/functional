<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Psalm\PartitionFunctionReturnTypeProvider;

/**
 * @see PartitionFunctionReturnTypeProvider
 *
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param callable(TV, TK): bool ...$predicates
 *
 * @psalm-return array<array-key, array<TK, TV>>
 */
function partition(iterable $collection, callable ...$predicates): array
{
    $predicateCount = count($predicates);
    $partitions = array_fill(0, $predicateCount + 1, []);

    foreach ($collection as $index => $element) {
        foreach ($predicates as $partition => $callback) {
            if (call_user_func($callback, $element, $index)) {
                $partitions[$partition][$index] = $element;
                continue 2;
            }
        }

        $partitions[$predicateCount][$index] = $element;
    }

    return $partitions;
}

/**
 * @psalm-template TK of array-key
 * @psalm-template TV
 *
 * @psalm-template TC1
 * @psalm-template TC2
 * @psalm-template TC3
 * @psalm-template TC4
 * @psalm-template TC5
 * @psalm-template TC6
 * @psalm-template TC7
 * @psalm-template TC8
 * @psalm-template TC9
 * @psalm-template TC10
 *
 * @psalm-param iterable<TK, TV> $collection
 * @psalm-param class-string<TC1> $class1
 * @psalm-param class-string<TC2> $class2
 * @psalm-param class-string<TC3> $class3
 * @psalm-param class-string<TC4> $class4
 * @psalm-param class-string<TC5> $class5
 * @psalm-param class-string<TC6> $class6
 * @psalm-param class-string<TC7> $class7
 * @psalm-param class-string<TC8> $class8
 * @psalm-param class-string<TC9> $class9
 * @psalm-param class-string<TC10> $class10
 *
 * @psalm-return (
 *     func_num_args() is 3 ? array{list<TC1>,list<TV>} : (
 *     func_num_args() is 4 ? array{list<TC1>,list<TC2>,list<TV>} : (
 *     func_num_args() is 5 ? array{list<TC1>,list<TC2>,list<TC3>,list<TV>} : (
 *     func_num_args() is 6 ? array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TV>} : (
 *     func_num_args() is 7 ? array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TC5>,list<TV>} : (
 *     func_num_args() is 8 ? array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TC5>,list<TC6>,list<TV>} : (
 *     func_num_args() is 9 ? array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TC5>,list<TC6>,list<TC7>,list<TV>} : (
 *     func_num_args() is 10 ? array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TC5>,list<TC6>,list<TC7>,list<TC8>,list<TV>} : (
 *     func_num_args() is 11 ? array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TC5>,list<TC6>,list<TC7>,list<TC8>,list<TC9>,list<TV>} : (
 *     array{list<TC1>,list<TC2>,list<TC3>,list<TC4>,list<TC5>,list<TC6>,list<TC7>,list<TC8>,list<TC9>,list<TC10>,list<TV>}
 * ))))))))))
 *
 * @psalm-suppress MoreSpecificReturnType,LessSpecificReturnStatement
 */
function partitionOf(
    iterable $collection,
    bool $invariant,
    string $class1,
    string $class2 = null,
    string $class3 = null,
    string $class4 = null,
    string $class5 = null,
    string $class6 = null,
    string $class7 = null,
    string $class8 = null,
    string $class9 = null,
    string $class10 = null
): array
{
    $partitions = array_fill(0, func_num_args() - 1, []);

    $classes = func_get_args();
    array_shift($classes);
    array_shift($classes);

    foreach ($collection as $element) {
        /**
         * @var class-string $c
         */
        foreach ($classes as $partition => $c) {
            if (is_a($element, $c)) {
                $partitions[$partition][] = $element;
                continue 2;
            }
        }

        $partitions[func_num_args() - 2][] = $element;
    }

    return $partitions;
}

