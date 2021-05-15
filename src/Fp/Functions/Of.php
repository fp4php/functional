<?php

declare(strict_types=1);

namespace Fp;

/**
 * @psalm-template T of object
 * @psalm-template TO

 * @psalm-param T $subject
 * @psalm-param class-string<TO> $fqcn
 *
 * @psalm-return bool
 *
 * @psalm-assert-if-true TO $subject
 */
function of(object $subject, string $fqcn, bool $invariant = false): bool
{
    return $invariant
        ? $subject::class === $fqcn
        : is_a($subject, $fqcn);
}
