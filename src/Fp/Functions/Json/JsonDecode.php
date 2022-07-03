<?php

declare(strict_types=1);

namespace Fp\Json;

use Fp\Functional\Either\Either;

/**
 * Decode json string into associative array
 * Returns Left on error
 *
 * ```php
 * >>> jsonDecode('{"a": [{"b": true}]}')->get();
 * => ['a' => [['b' => true]]]
 * ```
 *
 * @param string $json
 * @return Either<string, array|scalar>
 */
function jsonDecode(string $json): Either
{
    /** @var array $decoded */
    $decoded = json_decode(json: $json, associative: true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return Either::left(json_last_error_msg());
    }

    return Either::right($decoded);
}
