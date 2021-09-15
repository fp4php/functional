<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;

use function Fp\Evidence\proveTrue;

final class ProveTrueStaticTest
{
    /**
     * @param null|int $number
     * @return Option<int>
     */
    public function testAssertNotNullWithOption(?int $number): Option
    {
        return Option::do(function () use ($number) {
            yield proveTrue(null !== $number);

            return $number;
        });
    }

    /**
     * @param array{name?: string} $hasName
     * @return Option<string>
     */
    public function testAssertKeyExistsWithOption(array $hasName): Option
    {
        return Option::do(function() use ($hasName) {
            yield proveTrue(array_key_exists("name", $hasName));

            return $hasName["name"];
        });
    }

    /**
     * @param int|null $number
     * @return Either<"not_number", int>
     */
    public function testAssertNotNullWithEither(?int $number): Either
    {
        return Either::do(function() use ($number) {
            yield proveTrue(null !== $number)->toRight(fn() => "not_number");

            return $number;
        });
    }

    /**
     * @param array{name?: string} $hasName
     * @return Either<"no_prop", string>
     */
    public function testAssertKeyExistsWithEither(array $hasName): Either
    {
        return Either::do(function() use ($hasName) {
            yield proveTrue(array_key_exists("name", $hasName))->toRight(fn() => "no_prop");

            return $hasName["name"];
        });
    }
}
