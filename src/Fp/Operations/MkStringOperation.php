<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class MkStringOperation extends AbstractOperation
{
    public function __invoke(string $start, string $sep, string $end): string
    {
        /** @var string */
        $emptyString = '';

        $fold = new FoldOperation(
            iterator: IntersperseOperation::of($this->gen)($sep),
            init: $emptyString,
        );

        return $start . $fold(fn($acc, $cur) => $acc . (string) $cur) . $end;
    }
}
