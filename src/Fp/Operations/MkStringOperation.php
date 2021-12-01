<?php

declare(strict_types=1);

namespace Fp\Operations;

/**
 * @template TK
 * @template TV
 * @psalm-immutable
 * @extends AbstractOperation<TK, TV>
 */
class MkStringOperation extends AbstractOperation
{
    public function __invoke(string $start, string $sep, string $end): string
    {
        $interspersed = IntersperseOperation::of($this->gen)($sep);
        $reduced = FoldOperation::of($interspersed)(
            '',
            fn(string $acc, $cur) => $acc . (string) $cur
        );

        return $start . $reduced . $end;
    }
}
