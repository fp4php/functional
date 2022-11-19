<?php

declare(strict_types=1);

namespace Fp\Operations;

use function Fp\Collection\fold;

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
        $fold = fold('', IntersperseOperation::of($this->gen)($sep))(fn($acc, $cur) => $acc . (string) $cur);
        return $start . $fold . $end;
    }
}
