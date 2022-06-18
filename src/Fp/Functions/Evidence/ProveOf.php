<?php

declare(strict_types=1);

namespace Fp\Evidence;

use Fp\Functional\Option\Option;

use function Fp\of;

/**
 * Prove that subject is of given class
 *
 * ```php
 * >>> proveOf(new Foo(1), Foo::class);
 * => Some(Foo(1))
 *
 * >>> proveOf(new Bar(2), Foo::class);
 * => None
 * ```
 *
 * @template TV
 * @template TVO
 *
 * @param TV $subject
 * @param class-string<TVO> $fqcn fully qualified class name
 * @return Option<TVO>
 */
function proveOf(mixed $subject, string $fqcn, bool $invariant = false): Option
{
    /** @var Option<TVO> */
    return of($subject, $fqcn, $invariant)
        ? Option::some($subject)
        : Option::none();
}
