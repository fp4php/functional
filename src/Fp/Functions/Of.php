<?php

declare(strict_types=1);

namespace Fp;

/**
 * @psalm-template T
 * @psalm-template TO

 * @psalm-param T $subject
 * @psalm-param class-string<TO> $fqcn
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
