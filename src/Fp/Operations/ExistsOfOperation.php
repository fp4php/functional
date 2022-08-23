<?php

declare(strict_types=1);

namespace Fp\Operations;

use function Fp\Evidence\proveOf;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class ExistsOfOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     */
    public function __invoke(string|array $fqcn, bool $invariant = false): bool
    {
        foreach ($this->gen as $item) {
            if (proveOf($item, $fqcn, $invariant)->isSome()) {
                return true;
            }
        }

        return false;
    }
}
