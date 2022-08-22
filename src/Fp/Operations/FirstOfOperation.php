<?php

declare(strict_types=1);

namespace Fp\Operations;

use Fp\Functional\Option\Option;

use function Fp\Evidence\proveOf;

/**
 * @template TK
 * @template TV
 *
 * @extends AbstractOperation<TK, TV>
 */
final class FirstOfOperation extends AbstractOperation
{
    /**
     * @template TVO
     *
     * @param class-string<TVO>|list<class-string<TVO>> $fqcn
     * @return Option<TVO>
     */
    public function __invoke(string|array $fqcn, bool $invariant = false): Option
    {
        foreach ($this->gen as $value) {
            $option = proveOf($value, $fqcn, $invariant);

            if ($option->isSome()) {
                return $option;
            }
        }

        return Option::none();
    }
}
