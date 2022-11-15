<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

/**
 * Prove that given collection is of array type
 *
 * ```php
 * >>> proveArray([]);
 * => Some([])
 *
 * >>> proveArray([1, 2]);
 * => Some([1, 2])
 *
 * >>> proveArray(true);
 * => None
 *
 * >>> proveArray([1, 2], vType: proveInt(...))
 * => Some([1, 2])
 *
 * >>> proveArray(['fst', 'snd'], vType: proveInt(...))
 * => None
 * >>> proveArray([1, 2], vType: proveInt(...))
 * => Some([1, 2])
 *
 * >>> proveArray(['fst', 'snd'], kType: proveInt(...))
 * => Some(['fst', 'snd'])
 *
 * >>> proveArray(['first' => 'fst', 'second' => 'snd'], kType: proveInt(...))
 * => None
 *
 * >>> proveArray(['first' => 'fst', 'second' => 'snd'], kType: proveString(...), vType: proveString(...))
 * => Some(['first' => 'fst', 'second' => 'snd'])
 *
 * >>> proveArray(['first' => 1, 'second' => 2], kType: proveString(...), vType: proveString(...))
 * => None
 * ```
 *
 * @template TK
 * @template TV
 * @template TKO of array-key
 * @template TVO
 *
 * @param mixed|iterable<TK, TV> $value
 * @param null|callable(mixed): Option<TKO> $kType
 * @param null|callable(mixed): Option<TVO> $vType
 * @psalm-return (
 *     $kType is null
 *         ? ($vType is null ? Option<array<TK, TV>> : Option<array<TK, TVO>>)
 *         : ($vType is null ? Option<array<TKO, TV>> : Option<array<TKO, TVO>>)
 * )
 *
 * @psalm-suppress MixedAssignment, PossiblyNullArrayOffset, InvalidReturnStatement, InvalidReturnType
 */
function proveArray(mixed $value, null|callable $kType = null, null|callable $vType = null): Option
{
    if (!is_array($value)) {
        return Option::none();
    }

    $kType = null === $kType ? fn(string|int $value): Option => Option::some($value) : $kType;
    $vType = null === $vType ? fn(mixed $value): Option => Option::some($value) : $vType;

    $casted = [];

    /**
     * @var mixed $item
     */
    foreach ($value as $key => $item) {
        $kResult = $kType($key);
        $vResult = $vType($item);

        if ($kResult->isNone() || $vResult->isNone()) {
            return Option::none();
        }

        $casted[$kResult->get()] = $vResult->get();
    }

    return Option::some($casted);
}

/**
 * Prove that given collection is of non-empty-array type
 *
 * ```php
 * >>> proveNonEmptyArray([]);
 * => None
 *
 * >>> proveNonEmptyArray([1, 2]);
 * => Some([1, 2])
 *
 * >>> proveNonEmptyArray(true);
 * => None
 *
 * >>> proveNonEmptyArray([1, 2], vType: proveInt(...))
 * => Some([1, 2])
 *
 * >>> proveNonEmptyArray(['fst', 'snd'], vType: proveInt(...))
 * => None
 * >>> proveNonEmptyArray([1, 2], vType: proveInt(...))
 * => Some([1, 2])
 *
 * >>> proveNonEmptyArray(['fst', 'snd'], kType: proveInt(...))
 * => Some(['fst', 'snd'])
 *
 * >>> proveNonEmptyArray(['first' => 'fst', 'second' => 'snd'], kType: proveInt(...))
 * => None
 *
 * >>> proveNonEmptyArray(['first' => 'fst', 'second' => 'snd'], kType: proveString(...), vType: proveString(...))
 * => Some(['first' => 'fst', 'second' => 'snd'])
 *
 * >>> proveNonEmptyArray(['first' => 1, 'second' => 2], kType: proveString(...), vType: proveString(...))
 * => None
 * ```
 *
 * @template TK
 * @template TV
 * @template TKO of array-key
 * @template TVO
 *
 * @param mixed|iterable<TK, TV> $value
 * @param null|callable(mixed): Option<TKO> $kType
 * @param null|callable(mixed): Option<TVO> $vType
 * @psalm-return (
 *     $kType is null
 *         ? ($vType is null ? Option<non-empty-array<TK, TV>> : Option<non-empty-array<TK, TVO>>)
 *         : ($vType is null ? Option<non-empty-array<TKO, TV>> : Option<non-empty-array<TKO, TVO>>)
 * )
 */
function proveNonEmptyArray(mixed $value, null|callable $kType = null, null|callable $vType = null): Option
{
    return proveArray($value, $kType, $vType)->filter(fn($array) => !empty($array));
}
