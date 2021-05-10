<?php

declare(strict_types=1);

namespace Fp\Function\String;

use Fp\Functional\Option\Option;

use function JmesPath\search;

/**
 * @psalm-return Option<array|scalar>
 */
function jsonSearch(string $expr, array|string $data): Option
{
    $decoded = is_string($data) ? jsonDecode($data) : $data;

    /** @var array|scalar|null $result */
    $result = search($expr, $decoded);

    return Option::of($result);
}
