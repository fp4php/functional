<?php

declare(strict_types=1);

namespace Fp\Collection;

use Fp\Functional\Option\Option;
use Fp\Operations\TraverseOptionOperation;

use function Fp\Cast\asArray;

/**
 * It is equivalent to:
 * ```
 * $a = [
 *     Option::some(1),
 *     Option::some(2),
 *     Option::some(3),
 * ];
 *
 * assertEquals(
 *     Option::some([1, 2, 3]),
 *     traverseOption($a, fn($i) => $i),
 * );
 * ```
 *
 * Example:
 * ```
 * assertEquals(
 *     Option::some([1, 2, 3]),
 *     sequenceOption([
 *         Option::some(1),
 *         Option::some(2),
 *         Option::some(3),
 *     ]),
 * );
 *
 * assertEquals(
 *     Option::none(),
 *     sequenceOption([
 *         Option::some(1),
 *         Option::some(2),
 *         Option::none(),
 *     ]),
 * );
 * ```
 *
 * @template TK of array-key
 * @template TVI
 *
 * @param iterable<TK, Option<TVI>> $collection
 * @return (
 *    $collection is non-empty-list  ? Option<non-empty-list<TVI>>      :
 *    $collection is list            ? Option<list<TVI>>                :
 *    $collection is non-empty-array ? Option<non-empty-array<TK, TVI>> :
 *    Option<array<TK, TVI>>
 * )
 */
function sequenceOption(iterable $collection): Option
{
    return TraverseOptionOperation::id($collection)->map(fn($gen) => asArray($gen));
}
