<?php

declare(strict_types=1);

namespace Fp\Function\Json;

use Fp\Functional\Either\Right;
use Fp\Functional\Option\Option;

use function JmesPath\search;

/**
 * JsonPath search
 * @see jmespath
 *
 * @psalm-return Option<array|scalar>
 */
function jsonSearch(string $expr, array|string $data): Option
{
    $decoded = is_string($data)
        ? jsonDecode($data)
        : Right::of($data);

    return $decoded
        ->toOption()
        ->map(function (array $decoded) use ($expr) {
            /** @var array|scalar|null $result */
            $result = search($expr, $decoded);

            return $result;
        });
}
