<?php

declare(strict_types=1);

namespace Fp\Util;

use JsonException;
use Fp\Functional\Either\Either;

use function Fp\Evidence\proveArray;


/**
 * Decode json string into php value.
 * Returns Left on error.
 *
 * ```php
 * >>> jsonDecode('[1, 2, 3]');
 * => Right([1, 2, 3])
 *
 * >>> jsonDecode('"string literal"');
 * => Right("string literal")
 *
 * >>> jsonDecode('"string literal')->get();
 * => Left(JsonException('Syntax error'))
 * ```
 *
 * @param int<1, 2147483647> $depth
 * @return Either<JsonException, mixed>
 */
function jsonDecode(string $json, int $depth = 512, int $flags = 0): Either
{
    /** @var mixed $decoded */
    $decoded = json_decode(json: $json, associative: true, depth: $depth, flags: $flags);

    return json_last_error() !== JSON_ERROR_NONE
        ? Either::left(new JsonException(json_last_error_msg()))
        : Either::right($decoded);
}

/**
 * Decode json string into associative array.
 * Returns Left on error.
 *
 * ```php
 * >>> jsonDecodeArray('{"a": [{"b": true}]}');
 * => Right(['a' => [['b' => true]]])
 *
 * >>> jsonDecodeArray('{"a": [{"b": true');
 * => Left(JsonException('Syntax error'))
 *
 * >>> jsonDecodeArray('"string literal"')->get();
 * => Left(JsonException('Value is not array'))
 * ```
 *
 * @return Either<JsonException, array>
 */
function jsonDecodeArray(string $json): Either
{
    return jsonDecode($json)->flatMap(
        fn($decoded) => proveArray($decoded)->toRight(fn() => new JsonException('Value is not array')),
    );
}

/**
 * Encode given value to json.
 *
 * ```php
 * >>> jsonEncode([1, 2, 3]);
 * => '[1, 2, 3]'
 * ```
 *
 * @param int<1, 2147483647> $depth
 */
function jsonEncode(mixed $value, int $flags = JSON_THROW_ON_ERROR, int $depth = 512): string
{
    return json_encode($value, $flags, $depth);
}
