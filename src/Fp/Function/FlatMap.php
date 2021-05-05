<?php

declare(strict_types=1);

namespace Fp\Function;

/**
 * @psalm-template TK of array-key
 * @psalm-template TVI
 * @psalm-template TVO
 *
 * @psalm-param iterable<TK, TVI> $collection
 * @psalm-param callable(TVI, TK): iterable<array-key, TVO> $callback
 *
 * @psalm-return list<TVO>
 */
function flatMap(iterable $collection, callable $callback): array
{
    $flattened = [];

    foreach ($collection as $index => $element) {
        $result = call_user_func($callback, $element, $index);

        foreach ($result as $item) {
            $flattened[] = $item;
        }
    }

    return $flattened;
}
