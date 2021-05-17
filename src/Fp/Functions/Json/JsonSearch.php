<?php

declare(strict_types=1);

namespace Fp\Json;

use Fp\Functional\Either\Right;
use Fp\Functional\Option\Option;

use function JmesPath\search;

/**
 * Search by JsonPath expression
 * Returns None if there is no data by given expression
 *
 * @psalm-param string $expr json path expression
 * @psalm-param array|string $data json-string or decoded into associative array json
 *
 * @psalm-return Option<array|scalar>
 *
 * @see jmespath
 */
function jsonSearch(string $expr, array|string $data): Option
{
    $decoded = is_string($data)
        ? jsonDecode($data)
        : Right::of($data);

    return $decoded
        ->toOption()
        ->map(function (array|int|float|string|bool $decoded) use ($expr) {
            /** @var array|scalar|null $result */
            $result = search($expr, $decoded);

            return $result;
        });
}
