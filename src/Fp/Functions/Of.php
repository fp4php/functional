<?php

declare(strict_types=1);

namespace Fp;

use Fp\Functional\Option\Option;

use function Fp\Evidence\proveClassString;
use function Fp\Evidence\proveObject;

/**
 * Check if subject is of given class
 *
 * REPL:
 * >>> of(new Foo(1), Foo::class);
 * => true
 * >>> of(SubFoo::class, Foo::class);
 * => true
 *
 *
 * @psalm-template TO
 * @psalm-param mixed $subject
 * @psalm-param class-string<TO> $fqcn
 * @psalm-param bool $invariant if turned on then subclasses are not allowed
 * @psalm-assert-if-true TO $subject
 */
function of(mixed $subject, string $fqcn, bool $invariant = false): bool
{
    $subjectOption = Option::fromNullable($subject);

    $objectFilter = fn(): Option => $subjectOption
        ->flatMap(fn($subj) => proveObject($subj))
        ->map(fn(object $object) => $invariant
            ? $object::class === $fqcn
            : is_a($object, $fqcn)
        );

    $classStringFilter = fn(): Option => $subjectOption
        ->flatMap(fn($subj) => proveClassString($subj))
        ->map(fn(string $classString) => $invariant
            ? $classString === $fqcn
            : is_a($classString, $fqcn, true)
        );

    return $objectFilter()
        ->orElse($classStringFilter)
        ->getOrElse(false);
}
