<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param \Closure(TVI, TK): iterable<array-key, TVO> $callback
 *
 * @psalm-return list<TVO>
 */
function flatMap(iterable $collection, \Closure $callback): array
{
    $flattened = [];

    foreach ($collection as $index => $element) {
        $result = $callback($element, $index);

        foreach ($result as $item) {
            $flattened[] = $item;
        }
    }

    return $flattened;
}
