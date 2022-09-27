<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Collections\HashTable;
use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class UniqueByOperation extends AbstractOperation
{
    /**
     * @param callable(TV): mixed $callback
     * @return Generator<TK, TV>
     */
    public function __invoke(callable $callback): Generator
    {
        $seen = new HashTable();

        foreach ($this->gen as $key => $item) {
            /** @var mixed $byValue */
            $byValue = $callback($item);

            if ($seen->get($byValue)->isSome()) {
                continue;
            }

            $seen->update($byValue, true);

            yield $key => $item;
        }
    }
}
