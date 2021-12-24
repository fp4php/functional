<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

use function Fp\Cast\asGenerator;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class ChunksOperation extends AbstractOperation
{
    /**
     * @psalm-pure
     * @psalm-template TPreserve of bool
     * @psalm-param TPreserve $preserveKeys
     * @psalm-param positive-int $size
     * @psalm-return (TPreserve is true
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
