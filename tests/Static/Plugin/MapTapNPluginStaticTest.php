<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Functional\Option\Option;
use Tests\Mock\Foo;

final class MapTapNPluginStaticTest
{
    public static function methodWithOptionalParams(int $a, bool $b = true, bool $c = true): Foo
    {
        return new Foo($a, $b, $c);
    }

    public static function methodWithRegularParams(int $a, bool $b, bool $c): Foo
    {
        return new Foo($a, $b, $c);
    }

    /**
     * @return list<int>
     * @no-named-arguments
     */
    public static function methodWithVariadicParam(int $a, int $b, int $c, int ...$rest): array
    {
        return [$a, $b, $c, ...$rest];
    }

    /**
     * @param Option<array{int, bool, bool}> $args
     * @return Option<Foo>
     */
    public static function testPutAllRequiredArgumentsToRegularMethod(Option $args): Option
    {
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{int, bool, int}> $args
     * @return Option<Foo>
     */
    public static function testPutInvalidArgumentToRegularMethod(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{int, bool}> $args
     * @return Option<Foo>
     */
    public static function testPutTwoArgumentInsteadThreeToRegularMethod(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{int}> $args
     * @return Option<Foo>
     */
    public static function testPutOneArgumentInsteadThreeToRegularMethod(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{int, bool, bool}> $args
     * @return Option<Foo>
     */
    public static function testPutOneRequiredAndTwoOptionalArgumentsToMethodWithOptionalParams(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int, bool}> $args
     * @return Option<Foo>
     */
    public static function testPutOneRequiredAndOneOptionalArgumentsToMethodWithOptionalParams(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int}> $args
     * @return Option<Foo>
     */
    public static function testPutOneRequiredAndNoOptionalArgumentsToMethodWithOptionalParams(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int, string}> $args
     * @return Option<Foo>
     */
    public static function testPutOneInvalidArgumentToMethodWithOptionalParams(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int, int, string}> $args
     * @return Option<Foo>
     */
    public static function testPutTwoInvalidArgumentToMethodWithOptionalParams(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<int> $args
     * @return Option<Foo>
     */
    public static function testTemplateIsNotTuple(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{int, int}> $args
     * @return Option<list<int>>
     */
    public static function testForgetOneRequiredArgumentForMethodWithVariadicParam(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithVariadicParam(...));
    }

    /**
     * @param Option<array{int}> $args
     * @return Option<list<int>>
     */
    public static function testForgetTwoRequiredArgumentForMethodWithVariadicParam(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithVariadicParam(...));
    }

    /**
     * @param Option<array{int, int, int}> $args
     * @return Option<list<int>>
     */
    public static function testPutOnlyRequiredArgumentsForMethodWithVariadicParam(Option $args): Option
    {
        return $args->mapN(self::methodWithVariadicParam(...));
    }

    /**
     * @param Option<array{int, int, int, int}> $args
     * @return Option<list<int>>
     */
    public static function testPutRequiredArgumentsAndOneVariadicToMethodWithVariadicParam(Option $args): Option
    {
        return $args->mapN(self::methodWithVariadicParam(...));
    }

    /**
     * @param Option<array{int, int, int, int, int, int}> $args
     * @return Option<list<int>>
     */
    public static function testPutRequiredArgumentsAndManyVariadicToMethodWithVariadicParam(Option $args): Option
    {
        return $args->mapN(self::methodWithVariadicParam(...));
    }

    /**
     * @param Option<array{int, int, int, int, int, int, string}> $args
     * @return Option<list<int>>
     */
    public static function testPutRequiredArgumentsAndManyVariadicWithInvalidArgumentToMethodWithVariadicParam(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithVariadicParam(...));
    }

    /**
     * @param Option<array{a: int, b: bool, c: bool}> $args
     * @return Option<Foo>
     */
    public static function testPutAllRequiredArgumentsToRegularMethodUsingShape(Option $args): Option
    {
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{a: int, b: bool}> $args
     * @return Option<Foo>
     */
    public static function testForgetToPutOneRequiredArgumentsToRegularMethodUsingShape(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{a: int}> $args
     * @return Option<Foo>
     */
    public static function testForgetToPutTwoRequiredArgumentsToRegularMethodUsingShape(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{a: int, b: bool, c: string}> $args
     * @return Option<Foo>
     */
    public static function testPutAllRequiredArgumentsWithOneInvalidArgumentToRegularMethodUsingShape(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{a: int, b: string, c: string}> $args
     * @return Option<Foo>
     */
    public static function testPutAllRequiredArgumentsWithTwoInvalidArgumentToRegularMethodUsingShape(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithRegularParams(...));
    }

    /**
     * @param Option<array{a: int}> $args
     * @return Option<Foo>
     */
    public static function testPutOnlyRequiredArgumentsToMethodWithOptionalParamsUsingShape(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{a: int, b: bool}> $args
     * @return Option<Foo>
     */
    public static function testPutOneRequiredArgumentAndOneOptionalArgumentToMethodWithOptionalParamsUsingShape(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{a: int, b: bool, c: bool}> $args
     * @return Option<Foo>
     */
    public static function testPutOneRequiredArgumentAndAllOptionalArgumentsToMethodWithOptionalParamsUsingShape(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{a: int, b: bool, c: string}> $args
     * @return Option<Foo>
     */
    public static function testPutAllArgumentsToMethodWithOptionalParamsAndOneInvalidUsingShape(Option $args): Option
    {
        /** @psalm-suppress IfThisIsMismatch */
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{a: int, b: bool, c: bool, d: string}> $args
     * @return Option<Foo>
     */
    public static function testPutAllArgumentsToMethodWithOptionalParamsAndOneUnknownUsingShape(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int, bool, bool, string, int}> $args
     * @return Option<Foo>
     */
    public static function testPutAllArgumentsAndTwoUnnecessaryArgumentToMethodWithOptionalParams(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int, bool, bool, string}> $args
     * @return Option<Foo>
     */
    public static function testPutAllArgumentsAndOneUnnecessaryArgumentToMethodWithOptionalParams(Option $args): Option
    {
        return $args->mapN(self::methodWithOptionalParams(...));
    }

    /**
     * @param Option<array{int, bool, bool, string}> $args
     * @return Option<Foo>
     */
    public static function testPutMoreThanRequiredArgumentToRegularMethod(Option $args): Option
    {
        return $args->mapN(self::methodWithRegularParams(...));
    }
}
