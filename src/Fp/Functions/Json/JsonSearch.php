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
 * REPL:
 * >>> jsonSearch('a[0].b', ['a' => [['b' => true]]]);
 * => true
 * >>> jsonSearch('a[0].b', '{"a": [{"b": true}]}');
 * => true
 *
 * @deprecated will be removed
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
    $decodedEither = is_string($data)
        ? jsonDecode($data)
        : Right::of($data);

    return Option::do(function () use ($decodedEither, $expr) {
        $decoded = yield $decodedEither->toOption();

        /** @psalm-var array|scalar|null $nullableResult */
        $nullableResult = search($expr, $decoded);

        return yield Option::fromNullable($nullableResult);
    });
}
