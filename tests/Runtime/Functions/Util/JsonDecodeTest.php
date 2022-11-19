<?php

declare(strict_types=1);

namespace Tests\Runtime\Functions\Util;

use JsonException;
use Fp\Functional\Either\Either;
use PHPUnit\Framework\TestCase;

use function Fp\Util\jsonDecode;
use function Fp\Util\jsonDecodeArray;
use function Fp\Util\jsonEncode;

final class JsonDecodeTest extends TestCase
{
    /**
     * @param Either<JsonException, mixed> $expected
     * @dataProvider jsonDecodeProvider
     */
    public function testJsonDecode(string $json, Either $expected): void
    {
        $this->assertEquals($expected, jsonDecode($json));
    }

    public function jsonDecodeProvider(): iterable
    {
        yield 'Util array' => [
            '[1, 2, 3]',
            Either::right([1, 2, 3]),
        ];

        yield 'String literal' => [
            '"string literal"',
            Either::right('string literal'),
        ];

        yield 'Invalid array' => [
            '[1, 2, 3',
            Either::left(new JsonException('Syntax error')),
        ];
    }

    /**
     * @param Either<JsonException, array> $expected
     * @dataProvider jsonDecodeArrayProvider
     */
    public function testJsonDecodeArray(string $json, Either $expected): void
    {
        $this->assertEquals($expected, jsonDecodeArray($json));
    }

    public function jsonDecodeArrayProvider(): iterable
    {
        yield 'Util array' => [
            '[1, 2, 3]',
            Either::right([1, 2, 3]),
        ];

        yield 'Non array json' => [
            '"string literal"',
            Either::left(new JsonException('Value is not array')),
        ];

        yield 'Invalid array' => [
            '[1, 2, 3',
            Either::left(new JsonException('Syntax error')),
        ];
    }

    public function testJsonEncode(): void
    {
        $this->assertEquals('[1,2,3]', jsonEncode([1, 2, 3]));
    }
}
