<?php

declare(strict_types=1);

namespace Fp\Operations;

use Generator;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class GroupAdjacentByOperation extends AbstractOperation
{
    /**
     * @template D
     *
     * @param callable(TV): D $discriminator
     * @return Generator<int, array{D, non-empty-list<TV>}>
     */
    public function __invoke(callable $discriminator): Generator
    {
        $buffer = [];
        $prevDisc = null;
        $isHead = true;

        foreach ($this->gen as $elem) {
            if ($isHead) {
                $isHead = false;
                $prevDisc = $discriminator($elem);
            }

            $curDisc = $discriminator($elem);

            if ($prevDisc !== $curDisc && !empty($buffer)) {
                yield [$prevDisc, $buffer];
                $buffer = [];
            }

            $buffer[] = $elem;
            $prevDisc = $curDisc;
        }

        if (!empty($buffer)) {
            yield [$prevDisc, $buffer];
        }
    }
}
