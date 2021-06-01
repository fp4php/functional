<?php

declare(strict_types=1);

namespace Tests\Static\Functions\Evidence;

use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;
use Tests\PhpBlockTestCase;

final class ProveTrueTest extends PhpBlockTestCase
{
    public function testProveTrueOf(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var null|int $number */
                $number = 0;

                $result = Fp\Functional\Option\Option::do(function() use ($number) {
                    yield Fp\Evidence\proveTrue(null !== $number);
            
                    return $number;
                });
            ',
            strtr('Option<int>', [
                'Option' => Option::class,
            ])
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var array{name?: string} $hasName */
                $hasName = [];

                $result = Fp\Functional\Option\Option::do(function() use ($hasName) {
                    yield Fp\Evidence\proveTrue(array_key_exists("name", $hasName));

                    return $hasName["name"];
                });
            ',
            strtr('Option<string>', [
                'Option' => Option::class,
            ])
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var null|int $number */
                $number = 0;

                $result = Fp\Functional\Either\Either::do(function() use ($number) {
                    yield Fp\Evidence\proveTrue(null !== $number)->toRight(fn() => "not_number");

                    return $number;
                });
            ',
            strtr('Either<"not_number", int>', [
                'Either' => Either::class,
            ])
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var array{name?: string} $hasName */
                $hasName = [];

                $result = Fp\Functional\Either\Either::do(function() use ($hasName) {
                    yield Fp\Evidence\proveTrue(array_key_exists("name", $hasName))->toRight(fn() => "no_prop");

                    return $hasName["name"];
                });
            ',
            strtr('Either<"no_prop", string>', [
                'Either' => Either::class,
            ])
        );
    }
}
