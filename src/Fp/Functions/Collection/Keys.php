<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Operations\KeysOperation;

use function Fp\Cast\asList;

/**
 * Returns list of collection keys
 *
 * ```php
 * >>> keys(['a' => 1, 'b' => 2]);
 * => ['a', 'b']
 * ```
 *
 * @template TK
 *
 * @param iterable<TK, mixed> $collection
 * @return list<TK>
 * @psalm-return ($collection is non-empty-array ? non-empty-list<TK> : list<TK>)
 */
function keys(iterable $collection): array
{
    return asList(KeysOperation::of($collection)());
}
