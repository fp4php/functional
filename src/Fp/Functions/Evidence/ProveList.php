<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

use function Fp\Collection\traverseOption;

/**
 * Prove that given collection is of list type
 *
 * ```php
 * >>> proveList([1, 2]);
 * => Some([1, 2])
 *
 * >>> proveList([1, 2 => 2]);
 * => None
 *
 * >>> proveList([1, 2], proveInt(...));
 * => Some([1, 2])
 *
 * >>> proveList(['1', '2'], proveInt(...));
 * => None
 * ```
 *
 * @template TV
 * @template TVO
 *
 * @param mixed|iterable<TV> $value
 * @param null|callable(mixed): Option<TVO> $vType
 * @return Option<list<TV>
 * @psalm-return ($vType is null ? Option<list<TV>> : Option<list<TVO>>)
 */
function proveList(mixed $value, null|callable $vType = null): Option
{
    return match (true) {
        !is_array($value) || !array_is_list($value) => Option::none(),
        null === $vType => Option::some($value),
        default => traverseOption($value, $vType),
    };
}

/**
 * Prove that given collection is of list type
 *
 * ```php
 * >>> proveNonEmptyList([]);
 * => None
 *
 * >>> proveNonEmptyList([1, 2]);
 * => Some([1, 2])
 *
 * >>> proveNonEmptyList([1, 2 => 2]);
 * => None
 *
 * >>> proveNonEmptyList([1, 2], proveInt(...));
 * => Some([1, 2])
 *
 * >>> proveNonEmptyList(['1', '2'], proveInt(...));
 * => None
 * ```
 *
 * @template TV
 * @template TVO
 *
 * @param mixed|iterable<TV> $value
 * @param null|callable(mixed): Option<TVO> $vType
 * @return Option<list<TV>
 * @psalm-return ($vType is null ? Option<non-empty-list<TV>> : Option<non-empty-list<TVO>>)
 */
function proveNonEmptyList(mixed $value, null|callable $vType = null): Option
{
    return proveList($value, $vType)->filter(fn($list) => !empty($list));
}
