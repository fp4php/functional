<?php

declare(strict_types=1);

namespace Fp\Json;

use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;

/**
 * Decode json string into associative array
 * Returns Left on error
 *
 * @param string $json
 *
 * @return Either<string, array|scalar>
 */
function jsonDecode(string $json): Either
{
    /** @var array $decoded */
    $decoded = json_decode(json: $json, associative: true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return Left::of(json_last_error_msg());
    }

    return Right::of($decoded);
}
