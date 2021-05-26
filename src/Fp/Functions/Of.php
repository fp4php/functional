<?php

declare(strict_types=1);

namespace Fp;

/**
 * Check if subject is of given class
 *
 * REPL:
 * >>> of(new Foo(), Foo::class);
 * => true
 *
 *
 * @psalm-template T
 * @psalm-template TO

 * @psalm-param T $subject
 * @psalm-param class-string<TO> $fqcn
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 *
 * @psalm-return bool
 *
 * @psalm-assert-if-true TO $subject
 */
function of(mixed $subject, string $fqcn, bool $invariant = false): bool
{
    if (!is_object($subject)) {
        return false;
    }

    return $invariant
        ? $subject::class === $fqcn
        : is_a($subject, $fqcn);
}
