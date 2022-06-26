<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class ChunksOperation extends AbstractOperation
{
    /**
     * @template TPreserve of bool
     *
     * @param TPreserve $preserveKeys
     * @param positive-int $size
     * @return (TPreserve is true
     *     ? Generator<int, non-empty-array<TK, TV>>
     *     : Generator<int, non-empty-list<TV>>
     * )
     */
    public function __invoke(int $size, bool $preserveKeys = false): Generator
    {
        return asGenerator(function () use ($preserveKeys, $size) {
            $chunk = [];
            $i = 0;

            foreach ($this->gen as $key => $value) {
                $i++;

                if ($preserveKeys) {
                    $chunk[$key] = $value;
                } else {
                    $chunk[] = $value;
                }

                if (0 === $i % $size) {
                    yield $chunk;
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                yield $chunk;
            }
        });
    }
}
